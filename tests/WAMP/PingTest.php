<?php

class PingTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Thruway\Connection
     */
    private $_conn;

    function setUp() {
        $this->_conn = new \Thruway\Connection(
            array(
                "realm" => 'testRealm',
                "url" => 'ws://127.0.0.1:8080',
                "max_retries" => 0,
            )
        );
    }

    function testPing() {
        $this->_result = null;
        $this->_conn->on(
            'open',
            function (\Thruway\ClientSession $session) {
                $session->ping(2)->then(function () {
                        $this->_result = "pong received";
                        $this->_conn->close();
                    },
                    function () {
                        $this->_result = "timeout";
                        $this->_conn->close();
                    }
                );
            }
        );

        $this->_conn->open();

        $this->assertEquals("pong received", $this->_result);
    }

    public function testServerPing()
    {
        $this->_testResult = null;
        $this->_error = null;
        $this->_conn->on(
            'open',
            function (\Thruway\ClientSession $session) {
                $session->call('com.example.ping', [])->then(
                    function ($res) {
                        $this->_conn->close();
                        $this->_testResult = $res[0];
                    },
                    function ($error = null) {
                        $this->_conn->close();
                        $this->_error = $error;
                    }
                );
            }
        );

        $this->_conn->open();

        $this->assertNull($this->_error, "Error is null");
        $this->assertTrue(is_numeric($this->_testResult), "Server ping returned a sequence number");
    }
} 