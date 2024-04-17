local _M = {}

function _M.explode(div, str)
    local pos, result = 0, {}

    for st,sp in function() return string.find(str,div,pos,true) end do
        table.insert(result, string.sub(str, pos, st-1))
        pos = sp + 1
    end
  
    table.insert(result, string.sub(str, pos))
  
    return result
end

return _M