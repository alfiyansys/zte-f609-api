<?php
/**
 * @author alfiyansys (https://github.com/alfiyansys)
 */

namespace walangkaji\ZteF609\Request\Network;

use walangkaji\ZteF609\ZteApi;
use walangkaji\ZteF609\Constants;
use walangkaji\ZteF609\GlobalFunction as Func;

class LAN extends ZteApi
{
    public function __construct($parent)
    {
        $this->zte              = $parent;
        $this->NetworkInterface = new NetworkInterface($this);
        $this->UserInterface    = new UserInterface($this);
    }

    /**
     * Get device information
     *
     * @return object
     */
    public function dhcpServer()
    {
        $request = $this->zte->request($this->zte->modemUrl . Constants::NETWORK_LAN_DHCP_SERVER);
        $dom     = str_get_html($request);

		echo $dom;
    }