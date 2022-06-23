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
        $this->zte = $parent;
        $this->lan = $this;
    }

	public function lan2lanIsolation(){
		$request = $this->zte->request($this->zte->modemUrl . Constants::NETWORK_LAN_LAN2LAN_ISOLATION);
		$dom     = str_get_html($request);
		
		foreach ($dom->find('table#BpduConf tr') as $key) {
            $cari  = $key->find('td');
            $keys  = strtolower(str_replace(' ', '_', $cari[0]->plaintext));

			// need some tweaks and testing
			$value = html_entity_decode($key->find('td2 input', 0)->attr['value']);
			$data[$keys] = boolval($value);
        }

		// why encode and decode ? 
		return json_decode(json_encode($data));
	}

    public function dhcpServer()
    {
        $request = $this->zte->request($this->zte->modemUrl . Constants::NETWORK_LAN_DHCP_SERVER);
        $dom     = str_get_html($request);

		//echo $dom;
    }
	
}	