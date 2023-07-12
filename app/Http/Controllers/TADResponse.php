<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\FilterArgumentError;

class TADResponse extends Controller
{
    const XML_NO_DATA_FOUND = '<Row><Result>0</Result><Information>No data!</Information></Row>';
    const DEFAULT_XML_HEADER = '<?xml version="1.0" encoding="iso8859-1" standalone="no"?>';

    /**
     * @var string Response's XML header.
     */
    private $response_header;

    /**
     * @var string Response's XML body.
     */
    private $response_body;

    /**
     * @var string Response's encoding.
     */
    private $encoding;

    /**
     * @var boolean Indicates if Response's has no data (empty).
     */
    private $is_empty_response;

    /**
     * Initialize the class.
     *
     * @param string $response XML string that represents the TAD response.
     */
    public function __construct($response, $encoding)
    {
        $header = $this->extract_xml_header($response);

        if ('' === $header) {
            $this->set_header(self::DEFAULT_XML_HEADER);
        }

        $this->set_encoding($encoding);
        $this->set_response($response);
    }

    /**
     * Returns response formatted according $options.
     *
     * @param array $options format to apply on the response.
     * @return mixed Response formatted.
     */
    public function get_response(array $options = [])
    {
        if (!isset($options['format'])) {
            $options['format'] = 'xml';
        }

        return $this->{'to_' . $options['format']}();
    }

    /**
     * Sets response.
     *
     * @param string $response XML string.
     */
    public function set_response($response='')
    {
        if ($this->is_there_no_data($response)) {
            !$this->is_no_data_response($response) && $response = $this->build_no_data_response($response);
            $this->is_empty_response = true;
        } else {
            $this->is_empty_response = false;
        }

        $xml_header = $this->extract_xml_header($response);
        if ('' !== $xml_header && 0!== strcmp($this->response_header, $xml_header)) {
            $this->response_header = $xml_header;
        }

        $this->response_body = $response;
    }

    /**
     * Gets response's encoding.
     */
    public function get_encoding()
    {
        return $this->encoding;
    }

    /**
     * Sets response's encoding
     *
     * @param string $encoding encoding.
     */
    public function set_encoding($encoding)
    {
        $this->encoding = $encoding;
        $this->set_response_header_encoding($encoding);
    }

    /**
     * Returns response's header.
     *
     * @return string header.
     */
    public function get_header()
    {
        return $this->response_header;
    }

    /**
     * Sets response's header
     *
     * @param string $header header to be set.
     */
    public function set_header($header)
    {
        $this->response_header = $header;
    }

    /**
     * Returns a not sanitized response without header.
     *
     * @return string XML string.
     */
    public function get_response_body()
    {
        return $this->response_body;
    }

    /**
     * Tells if response stored by the class is empty (with no data).
     *
     * @return boolean <b><code>true</code></b> response is empty otherwise returns <b><code>false</code></b>.
     */
    public function is_empty_response()
    {
        return $this->is_empty_response;
    }

    /**
     * Returns response in XML format.
     *
     * @return string XML string.
     */
    public function to_xml()
    {
        return (string) $this;
    }

    /**
     * Returns response in JSON format.
     *
     * @return string JSON string generated.
     */
    public function to_json()
    {
        return $this->is_empty_response() ? '{}' : json_encode(simplexml_load_string((string) $this));
    }

    /**
     * Returns response in array format.
     *
     * @return array array generated.
     */
    public function to_array()
    {
        return $this->is_empty_response() ? [] : json_decode($this->to_json((string) $this), true);
    }

    /**
     * Return the numbers of nodes that the response has.
     *
     * @return int number of nodes.
     */
    public function count()
    {
        return $this->get_items_number($this->to_xml());
    }

    /**
     * Magic method to define a dynamic filter of type 'filter_by'. This filter allows you define
     * multiple filtering criterias.
     *
     * @param string $method method invoked.
     * @param array $args arguments passed.
     * @return string filtered XML string.
     * @throws FilterArgumentError
     * @throws \Exception
     */
    public function __call($method, $args)
    {
        $xml = $this->to_xml();

        if (preg_match('/filter_by_([a-zA-Z]+)/', $method)) {
            $filters = preg_split('/(by_|_and_)/i', $method, -1);
            unset($filters[0]);

            if (count($filters) !== count($args)) {
                throw new FilterArgumentError(
                    'Invalid number of arguments: '
                    . count($filters) . ' expected, '
                    . count($args) . ' given.'
                );
            }

            foreach ($filters as $filter) {
                $filter_args = $this->normalize_filter_args(array_shift($args));

                switch ($filter) {
                    case 'date':
                        $filter_regex = '/<DateTime>([0-9]{4}-[0-9]{2}-[0-9]{2})/';
                        break;
                    case 'time':
                        $filter_regex = '/([0-9]{2}:[0-9]{2}:[0-9]{2})<\/DateTime>/';
                        break;
                    case 'datetime':
                        $filter_regex = '/'
                            . '<DateTime>([0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2})<\/DateTime>'
                            . '/';
                        break;
                    default:
                        $filter_regex = '/<' . ucwords($filter) . '>(.*?)<\/' . ucwords($filter) . '>/si';
                        break;
                }

                $xml = $this->filter_xml($xml, $filter_regex, $filter_args);
            }

            $this->set_response($xml);

            return $this;
        } else {
            throw new \Exception('Unknown method ' . $method);
        }
    }

    /**
     * Magic method to get a TADResponse object in string format.
     *
     * @return type
     */
    public function __toString()
    {
        return $this->response_header . $this->sanitize_xml_string($this->response_body);
    }

    /**
     * Parses an XML string applying an specific filter.
     *
     * @param string $xml input XML string.
     * @param string $filter regex to be applied on input.
     * @param array $range boundaries searching criteria.
     * @param string $xml_init_row_tag XML root tag.
     * @return string XML string filtered.
     */
    public function filter_xml($xml, $filter, array $range=[], $xml_init_row_tag='<Row>')
    {
        $xml_header = $this->extract_xml_header($xml);

        $matches = [];
        $filtered_xml = self::XML_NO_DATA_FOUND;

        $rows = explode($xml_init_row_tag, $xml);
        $main_xml_init_tag = trim(array_shift($rows));
        $main_xml_end_tag = '' !== $main_xml_init_tag  ? '</' . str_replace('<', '', $main_xml_init_tag) : '';

        if ('' !== $main_xml_end_tag) {
            $rows[] = str_replace($main_xml_end_tag, '', array_pop($rows));
        }

        if (preg_match_all($filter, $xml, $matches)) {
            $indexes = array_keys(
                array_filter(
                    $matches[1],
                    function($data) use ($range) {
                        switch (count($range)) {
                            case 1:
                                if (isset($range['like'])) {
                                    $result = false === strpos($data, $range['like']) ? false : true;
                                } elseif (isset($range['start'])) {
                                    $result = is_numeric($data) ?
                                            $data >= $range['start'] :
                                            0 <= strcmp($data, $range['start']);
                                } else {
                                    $result = is_numeric($data) ?
                                            $data <= $range['end'] :
                                            0 >= strcmp($data, $range['end']);
                                }
                                break;
                            case 2:
                                $result = is_numeric($data) ?
                                    $data >= $range['start'] && $data <= $range['end'] :
                                    !(strcmp($data, $range['start']) < 0 || strcmp($data, $range['end']) > 0);
                                break;
                            default:
                                $result = false;
                        }

                        return $result;
                    }
                )
            );

                $filtered_xml = (
                    0 === count($indexes) ?
                    self::XML_NO_DATA_FOUND :
                    join(
                        '',
                        array_map(
                            function($index) use ($rows, $xml_init_row_tag) {
                                return $xml_init_row_tag . $rows[$index];
                            },
                            $indexes
                        )
                    )
                );
        }

        $xml = $xml_header . $main_xml_init_tag . trim($filtered_xml) . $main_xml_end_tag;

        return $this->sanitize_xml_string($xml);
    }

    /**
     * Gets response's XML header.
     *
     * @param string $xml XML string.
     * @return string XML header.
     */
    private function extract_xml_header(&$xml)
    {
        $xml_header = '';

        if (false !== strpos($xml, '?>')) {
            $xml_items = explode('?>', $xml);

            $xml_header = $xml_items[0] . '?>';
            $xml = $xml_items[1];
        }

        $xml = $this->sanitize_xml_string($xml);
        return trim($xml_header);
    }

    /**
     * Adds encoding to XML header.
     *
     * @param string $encoding encoding to be set in XML header.
     * @return string XML header with encoding.
     */
    private function set_response_header_encoding($encoding = 'utf-8')
    {
        $header = $this->response_header;

        if (is_null($header) || '' === $header) {
            $header ='<?xml version="1.0" encoding="' . $encoding . '" standalone="no"?>';
        } else {
            $header = preg_filter('/encoding="([^"]+)"/', 'encoding="' . $encoding . '"', $header);
        }

        $this->response_header = $header;
    }

    /**
     * Cleans a XML string from undesired chars (in this case EOL by default).
     *
     * @param string $xml string to be cleaned out.
     * @return string XML string cleaned out.
     */
    private function sanitize_xml_string($xml, array $undesired_chars = [ "\n", "\r", "\t" ])
    {
        return trim(str_replace($undesired_chars, '', $xml));
    }

    /**
     * Sets boundaries to be used as filter criteria.
     *
     * @param mixed $args boundaries.
     * @return array boundaries validated.
     */
    private function normalize_filter_args($args)
    {
        $normalized_filter_args = [];
        $valid_range_filter = ['start', 'end', 'like'];

        if (is_array($args)) {
            $args_keys = array_keys($args);
            array_walk(
                $args_keys,
                function ($item) use ($valid_range_filter) {
                    if (!in_array($item, $valid_range_filter)) {
                        throw new FilterArgumentError('Invalid range key ' . $item);
                    }
                }
            );

            isset($args['start']) ? $normalized_filter_args['start'] = $args['start'] : null;
            isset($args['end']) ? $normalized_filter_args['end'] = $args['end'] : null;
            isset($args['like']) ? $normalized_filter_args['like'] = $args['like'] : null;

        } else {
            $normalized_filter_args['start'] = $normalized_filter_args['end'] = $args;
        }

        return $normalized_filter_args;
    }

    /**
     * Tells if device's response returns an empty response, represented by an empty XML string
     * (a string with just an open and end tags).
     *
     * @param string $response device response.
     * @return boolean <b><code>true</code></b> if it is a empty response.
     */
    private function is_there_no_data($response)
    {
        return ($this->is_no_data_response($response) || 0 === $this->get_items_number($response));
    }

    /**
     * Returns the numbers of nodes that the XML response has.
     *
     * @param type $response XML string.
     * @return int number of nodes.
     */
    private function get_items_number($response)
    {
        if (is_null($response) || '' === trim($response)) {
            return 0;
        }

        $response = $this->sanitize_xml_string($response);
        $xml = new \SimpleXMLElement($response);
        $items_number = $xml->count();

        return $items_number;
    }

    /**
     * Generates a modified XML response with a NO DATA text.
     *
     * @param string $response device response.
     * @return string modified XML response.
     */
    private function build_no_data_response($response)
    {
        (is_null($response) || '' === trim($response)) ? $response = '<Response></Response>' : null;

        $response = $this->get_header() . $response;
        $pos = strpos($response, '>', strpos($response, '>') + 1);
        $no_data_response = substr_replace($response, self::XML_NO_DATA_FOUND, $pos + 1, false);

        return $no_data_response;
    }

    /**
     * Tells if response passed represents an empty XML response.
     *
     * @param type $response XML string to be evaluated.
     * @return boolean <b><code>true</code></b> if it is a empty response.
     */
    private function is_no_data_response($response)
    {
        return false !== strpos($response, self::XML_NO_DATA_FOUND);
    }
}
