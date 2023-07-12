<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\TADSoap;
use App\Providers\TADZKlib;
use App\Exceptions\ConnectionError;
use App\Exceptions\UnrecognizedArgument;
use App\Exceptions\UnrecognizedCommand;

class TAD extends Controller
{
    /**
     * Valid commands args array.
     *
     * @var array
     */
    static private $parseable_args = [
        'com_key', 'pin', 'time', 'template',
        'name', 'password', 'group', 'privilege',
        'card', 'pin2', 'tz1', 'tz2', 'tz3',
        'finger_id', 'option_name', 'date',
        'size', 'valid', 'value'
    ];

    /**
     * @var string Device ip address.
     */
    private $ip;

    /**
     * @var mixed Device internal id.
     */
    private $internal_id;

    /**
     * @var string Device description (just for info purposes).
     */
    private $description;

    /**
     * Security communication code (required for SOAP functions calls).
     *
     * @var mixed
     */
    private $com_key;

    /**
     * @var int Connection timeout in seconds.
     */
    private $connection_timeout;

    /**
     * @var string Encoding for XML commands and responses.
     */
    private $encoding;

    /**
     * @var int UDP port number.
     */
    private $udp_port;

    /**
     * Holds a <code>TADSoap</code> instance to talk to device via SOAP.
     *
     * @var object
     */
    private $tad_soap;

    /**
     * Holds <code>PHP_ZKLib</code> instance to talk to device via UDP.
     *
     * @var object
     */
    private $zklib;


    /**
     * Returns an array with a full list of commands available.
     *
     * @return array list of commands available.
     */
    public static function commands_available()
    {
        return array_merge(static::soap_commands_available(), static::zklib_commands_available());
    }

    /**
     * Returns an array with SOAP commands list available.
     *
     * @return array SOAP commands list.
     */
    public static function soap_commands_available(array $options = [])
    {
        return TADSoap::get_commands_available($options);
    }

    /**
     * Returns an array with PHP_ZKLib commands available.
     *
     * @return array PHP_ZHLib commands list.
     */
    public static function zklib_commands_available()
    {
        return TADZKLib::get_commands_available();
    }

    /**
     * Returns valid commands arguments list.
     *
     * @return array arguments list.
     */
    public static function get_valid_commands_args()
    {
        return self::$parseable_args;
    }

    /**
     * Tells if device is "online" to process commands requests.
     *
     * @param string $ip device ip address
     * @param int $timeout seconds to wait for device.
     * @return boolean <b>true</b> if device is alive, <b>false</b> otherwise.
     */
    public static function is_device_online($ip, $timeout = 1)
    {
        $handler = curl_init($ip);
        curl_setopt_array($handler, [ CURLOPT_TIMEOUT => $timeout, CURLOPT_RETURNTRANSFER => true ]);
        $response = curl_exec($handler);
        curl_close($handler);

        return (boolean)$response;
    }

    /**
     * Get a new TAD class instance.
     *
     * @param TADSoap $soap_provider code><b>TADSoap</b></code> class instance.
     * @param TADZKLib $zklib_provider <code><b>ZKLib</b></code> class instance.
     * @param array $options device parameters.
     */
    public function __construct(TADSoap $soap_provider, TADZKLib $zklib_provider, array $options = [])
    {
        $this->ip = $options['ip'];
        $this->internal_id = (integer) $options['internal_id'];
        $this->com_key = (integer) $options['com_key'];
        $this->description = $options['description'];
        $this->connection_timeout = (integer) $options['connection_timeout'];
        $this->encoding = strtolower($options['encoding']);
        $this->udp_port = (integer) $options['udp_port'];

        $this->tad_soap = $soap_provider;
        $this->zklib = $zklib_provider;
    }

    /**
     * Magic __call method overriding to define in runtime the methods should be called based on method invoked.
     * (Something like Ruby metaprogramming :-P). In this way, we decrease the number of methods required
     * (usually should be one method per SOAP or PHP_ZKLib command exposed).
     *
     * Note:
     *
     * Those methods that add, update o delete device information, call SOAP method <b><code>refresh_db()</code></b>
     * to properly update device database.
     *
     * @param string $command command to be invoked.
     * @param array $args commands args.
     * @return string device response in XML format.
     * @throws ConnectionError.
     * @throws UnrecognizedCommand.
     * @throws UnrecognizedArgument.
     */
    public function __call($command, array $args)
    {
        $command_args = count($args) === 0 ? [] : array_shift($args);

        $this->check_for_connection() &&
        $this->check_for_valid_command($command) &&
        $this->check_for_unrecognized_args($command_args);

        if (in_array($command, TADSoap::get_commands_available())) {
            $response = $this->execute_command_via_tad_soap($command, $command_args);
        } else {
            $response = $this->execute_command_via_zklib($command, $command_args);
        }

        $this->check_for_refresh_tad_db($command);

        return $response;
    }

    /**
     * Send a command to device using a <code>TADSoap</code> instance class.
     *
     * @param string $command command to be sending.
     * @param array $args command args.
     * @return string device response.
     */
    public function execute_command_via_tad_soap($command, array $args = [])
    {
        $command_args = $this->config_array_items(array_merge(['com_key' => $this->get_com_key()], $args));

        return $this->tad_soap->execute_soap_command($command, $command_args, $this->encoding);
    }

    /**
     * Send a command to device using <code>PHP_ZKLib</code> class.
     *
     * All responses generate by PHP_ZKLib class are not in XML format, it is used <code>build_command_response</code>
     * to build an XML response, just to keep the TAD class behavior. For this purpose, the method uses class constans
     * <code>ZKLib::XML_SUCCESS_RESPONSE</code> and <code>ZKLib::XML_FAIL_RESPONSE</code>.
     *
     * @param string $command command to be sending.
     * @param array $args command args.
     * @return string string device response.
     */
    public function execute_command_via_zklib($command, array $args = [])
    {
        $command_args = $this->config_array_items($args);
        $response = $this->zklib->{$command}(array_merge(['encoding'=>$this->encoding], $command_args));

        return $response;
    }

    /**
     * Returns device's IP address.
     *
     * @return string IP address.
     */
    public function get_ip()
    {
        return $this->ip;
    }

    /**
     * Returns device's internal code.
     *
     * @return int internal code.
     */
    public function get_internal_id()
    {
        return $this->internal_id;
    }

    /**
     * Returns device's comm code.
     *
     * @return int code.
     */
    public function get_com_key()
    {
        return $this->com_key;
    }

    /**
     * Returns device's string description.
     *
     * @return string device description.
     */
    public function get_description()
    {
        return $this->description;
    }

    /**
     * Returns device's connection timeout.
     *
     * @return int connection timeout.
     */
    public function get_connection_timeout()
    {
        return $this->connection_timeout;
    }

    /**
     * Returns device's encoding (used for SOAP requests and responses).
     *
     * @return string encoding.
     */
    public function get_encoding()
    {
        return $this->encoding;
    }

    /**
     * Return device's UDP port number.
     *
     * @return int port number.
     */
    public function get_udp_port()
    {
        return $this->udp_port;
    }

    /**
     * Tells if device is ready (alive) to process requests.
     *
     * @return boolean <b>true</b> if device is alive, <b>false</b> otherwise.
     */
    public function is_alive()
    {
        return static::is_device_online($this->get_ip(), $this->connection_timeout);
    }

    /**
     * Throws an Exception when device is not alive.
     *
     * @return boolean <b><code>true</code></b> if there is a connection with the device.
     * @throws ConnectionError
     */
    private function check_for_connection()
    {
        if (!$this->is_alive()) {
            throw new ConnectionError('connection error ' . $this->get_ip());
        }

        return true;
    }

    /**
     * Tells if the command requested is in valid commands set.
     *
     * @param string $command command requested.
     * @return boolean <code><b>true</b></code> if the command es known by the class.
     * @throws UnrecognizedCommand
     */
    private function check_for_valid_command($command)
    {
        $tad_commands = static::commands_available();

        if (!in_array($command, $tad_commands)) {
            throw new UnrecognizedCommand("Comando $command no reconocido!");
        }

        return true;
    }

    /**
     * Tells if the arguments supplied are in valid args set.
     *
     * @param array $args args array to be verified.
     * @return <b><code>true</code></b> if all args supplied are valid (known by the class).
     * @throws TAD\Exceptions\UnrecognizedArgument
     */
    private function check_for_unrecognized_args(array $args)
    {
        if (0 !== count($unrecognized_args = array_diff(array_keys($args), static::get_valid_commands_args()))) {
            throw new UnrecognizedArgument('Parámetro(s) desconocido(s): ' . join(', ', $unrecognized_args));
        }

        return true;
    }

    /**
     * Tells if it's necessary to do a device database update. To do this, the method verified the command
     * executed to see if it did any adding, deleting or updating of database device. In that case, a
     * <code>refesh_db</code> command is executed.
     *
     * @param string $command_executed command executed.
     */
    private function check_for_refresh_tad_db($command_executed)
    {
        preg_match('/^(set_|delete_)/', $command_executed) && $this->execute_command_via_tad_soap('refresh_db', []);
    }

    /**
     * Returns an array with all parseable_args, allowed by the class, initialized with specific values
     * passed through $values array. Those args not passed in method param will be set to null.
     *
     * @param array $values array values to be analized.
     * @return array array generated.
     */
    private function config_array_items(array $values)
    {
        $normalized_args = [];

        foreach (static::get_valid_commands_args() as $parseable_arg_key) {
            $normalized_args[$parseable_arg_key] =
                    isset($values[$parseable_arg_key]) ? $values[$parseable_arg_key] : null;
        }

        return $normalized_args;
    }
}
