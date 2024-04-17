local registry = require ('registry')
local config = require ('config')
local util = require ('util')

local function get_version()
    return registry.get(config.registry_root_path .. config.registry_key_version)
end

local function get_node_type()
    return registry.get(config.registry_root_path .. config.registry_key_node_type)
end

local function set_node_type(node_type)
    local result = false
    
    if registry.set(config.registry_root_path .. config.registry_key_node_type, node_type, registry.TYPE_STRING) then
        result = true
    end
    
    return result
end

local function is_super_node()
    local result = false
    local node_type = get_node_type()
    
    if node_type == config.node_type_super then
        result = true
    end
    
    return result
end

local function is_registered_in_network()
    local result = false
    local node_type = get_node_type()
    
    if node_type == config.node_type_normal or node_type == config.node_type_super then
        result = true
    end
    
    return result
end

local function is_installed()
    local result = false
    
    if get_version() then
        result = true
    end
    
    return result
end

local function install()
    local result = false
    
    if registry.set(config.registry_root_path .. config.registry_key_version, config.version, registry.TYPE_STRING) then
        result = true
    end
    
    return result
end

local function is_behind_nat()
    local result = true
    
    local ip_list = iptable.getlist()

    for i, ip in pairs(ip_list) do 
        if ip ~= '127.0.0.1' and string.sub(ip, 0, 3) ~= '10.' and string.sub(ip, 0, 8) ~= '192.168.' then    
            iptable = util.explode('.', ip)
            if iptable[1] ~= '172' or tonumber(iptable[2]) < 16 or tonumber(iptable[2]) > 31 then
                result = false
            end
        end
    end
    
    return result
end

local function register_in_network()
    
end

if not is_installed() and not install() then
    console.log('Can\'t install app')
    os.exit()
end

if not is_registered_in_network() and not register_in_network() then
    console.log('Can\'t register app')
    os.exit()
end

if is_super_node() then
    console.log('Run as super node')
    if is_behind_nat() then
        console.log('App behind nat')
        os.exit()
    end
    
    -- run as super node
else
    -- run as normal node
end

local tcpip_server = require ("supernode.tcpip.server")
tcpip_server.start()