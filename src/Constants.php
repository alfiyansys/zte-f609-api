<?php

namespace walangkaji\ZteF609;

/**
 * CONSTANTS CLASS
 */
class Constants
{
    const TEMPLATE           = '/template.gch';
    const PARAM              = '/getpage.gch?pid=1002&nextpage=';
    const DEVICE_INFORMATION = self::PARAM . 'status_dev_info_t.gch';
    const VOIP_STATUS        = self::PARAM . 'status_voip_4less_t.gch';
    const REBOOT             = self::PARAM . 'manager_dev_conf_t.gch';
    const WAN_CONNECTION     = self::PARAM . 'IPv46_status_wan2_if_t.gch';
    const PON_INFORMATION    = self::PARAM . 'pon_status_link_info_t.gch';
    const MOBILE_NETWORK     = self::PARAM . 'status_mobnet_info_t.gch';

	const NETWORK_LAN_LAN2LAN_ISOLATION	= self::PARAM . 'net_lanlanisolate_t.gch';
	const NETWORK_LAN_DHCP_SERVER		= self::PARAM . 'net_dhcp_dynamic_t.gch';
}
