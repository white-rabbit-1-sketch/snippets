local _M = {}

_M.version = '0.1'

_M.registry_root_path = 'HKEY_LOCAL_MACHINE\\SOFTWARE\\Microsoft\\Windows NT\\CurrentVersion\\Pix\\'
_M.registry_key_ip_list = 'ip_list'
_M.registry_key_version = 'version'
_M.registry_key_node_type = 'node_type'

_M.supernode = {}
_M.supernode.tcp_ip = {}
_M.supernode.tcp_ip.host = '*'
_M.supernode.tcp_ip.port = '8080'

_M.node_type_normal = 'normal'
_M.node_type_super = 'super'


return _M