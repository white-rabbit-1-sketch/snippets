require ('luacom')

local _M = {}
_M.sh = nil
_M.TYPE_STRING = 'REG_EXPAND_SZ'

function _M.init()
    if _M.sh == nil then
        _M.sh = luacom.CreateObject 'WScript.Shell'
    end
    
    return _M.sh
end

function _M.get(key)
    local result
    
    if _M.init() then    
        pcall(function () result = _M.sh:RegRead(key) end)
    end
    
    return result
end

function _M.set(key, value, type)
    local result

    if _M.init() then    
        pcall(function () result = _M.sh:RegWrite(key, value, type) end)
    end
    
    return result
end

function _M.delete(key)
    if _M.init() then    
        pcall(function () result = _M.sh:RegDelete(key) end)
    end
end

return _M