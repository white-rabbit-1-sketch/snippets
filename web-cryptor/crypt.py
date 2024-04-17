#!/usr/bin/python

"""
Скрипт принимает входным параметро html файл, на выходе получается файл содержащий закриптованый java script код. Если немножко подравить то можно использовать в различных целях (не только для хтмл).
Алгоритмы шифровки\дешифровки написаны на псевдо языке (синтаксис похож на Lisp), который интерпретируется транслятором в код Питона и код javascript (можно добавить любые другие языки при необходимости).
Так же кроме шифровки выполняется запутывание кода.
"""


import random
import json
import sha

"""
Шифровка. Работает по принципу бинарного дерева. Считается % вхождения каждого символа. Затем по убыванию каждому отличному от других символу присваивается число из бинарного дерева. 
"""
def crypt1(d2, data):
    cr = {}
    j = 0
    for i in xrange(len(d2)):
        cr[d2[i][0]] = ""
        if j != 0 and j % 10 == 0:
            j += 1
            cr[d2[i][0]] += str(j)
        elif j != 0:
            cr[d2[i][0]] += str(j)
        cr[d2[i][0]] += "0"
        
        j += 1
        
    odata = ""    
    for i in data:
        odata += cr[i]
          
    return odata   
        
def calc_text_distrib(text):
    def table(text):
        d = {}
        for i in text:
            try:
                d[i] += 1
            except:
                d[i] = 1
 
        size = 1.0 * len(text)
        for k in d.keys():
            d[k] /= size
        
        return d
    
    d = table(text)
    
    d2 = []
    for k in d.keys():
        d2.append([k, d[k]])
    
    def s_f(x, y):
        if x[1] == y[1]: return 0
        if x[1] < y[1]: return -1
        return 1
        
    d2.sort(s_f)
    d2.reverse()
    
    result_str = ""
    for i in xrange(len(d2)):
        result_str += str(d2[i][0]) + "~"

    return result_str, d2
    
def s2pcode(scode):
# strip comments
    text = ""
    for line in scode.split("\n"):
        line = line.strip()
        if not line or line[0] == '#': continue
        text += line + " "
    scode = text

# braces
    text = ""
    q = False
    for i in xrange(len(scode)):
        c = scode[i]
        if c == '"':
            q = not q
            text += c
            continue
            
        if not q:
            if c == "(": c = " [ "
            if c == ")": c = " ] "
            
        text += c
        
    scode = text

# quotes
    syn = []
    skip_quote = False
    text = ""
    for i in scode.split(" "):
        if not i:
            if skip_quote:
                text += " "
            continue

        if skip_quote:
            text += " "

        if not skip_quote:
            if i and i[0] == '"':
                i = i[1:]
                skip_quote = True

        if skip_quote:
            if i and i[-1] == '"':
                i = i[:-1]
                skip_quote = False

        text += i

        if not skip_quote:
            syn.append(text)
            text = ""

# commas
    text = ""
    for i in xrange(len(syn) - 1):
        if syn[i] == "[":
            text += syn[i]
            continue

        if syn[i] == "]":
            text += syn[i]
            if syn[i + 1] != "]": text += ", "
            continue

        text += '"' + syn[i] + '"'
        if syn[i + 1] != "]": text += ", "

    if syn:
        text += syn[-1]

    return text

    
#Запутываем код
def eval_shield(js_code):
    text = "eval(\"\""
    j = 0
    var = ""
    import random
    r = 2
    for i in js_code:
        if i == '\n': i = ''
        if i == '"': i = '\\"'
        if i == '\\': i = '\\\\'
        var += i
        if j == r:
            j = 0
            text += '+"'+var+'"'
            var = ""
        else:
            j += 1
    if var: text += '+"'+var+'"'
    
    text += ");"

    return text

def unescape_shield(js_code):
    text = "unescape"
    
    for i in js_code:
        text += "%%u00%2.2x" % (ord(i))
    
def read_prog(prog):
    return json.read(prog)

def py_translate(pcode):
    state = {}
    state["indent"] = 0
    
    ocode = ""
    for block in pcode:
        ocode += py_translate_block(block, state) + "\n"
        
    return ocode    

#Транслятор
def py_translate_block(b, state):
    tb = lambda b: py_translate_block(b, state)

    if type(b) in [int, str]: return b
        
    op = b[0]
    if op == "if":
        state["indent"] += 1     
        ocode = "if " + tb(b[1]) + ":\n"
        for block in b[2]:
            ocode += "\t" * state["indent"] + tb(block)
        state["indent"] -= 1    
        return ocode  
    
    if op == "for ":
        ocode = tb(b[1]) + "in" + " " + tb(b[2]) + ":\n"
        state["indent"] += 1         
        for block in b[4]:
            ocode += "\t" * state["indent"] + tb(block) + "\n"
        ocode += "\t" * state["indent"] + tb(b[3])     
        state["indent"] -= 1             
        return ocode
    

    if op == "def":
        state["indent"] +=1
        ocode = "def " + b[1] + "(" + ", ".join(b[2][:len(b[2])]) + "):\n"
        for block in b[3]:
            ocode += "\t" * state["indent"] + tb(block) + "\n"
        state["indent"] -=1
        return ocode
    if op == "call":
        for x in xrange(len(b)):
            b[x] = tb(b[x])    
        return b[1] + "(" + ", ".join(b[2:]) + ")"     
    
    
        
    if op == "ret":
        return "return " + tb(b[1])

        
    if op == "inline":
        return b[1]    

        
    if op == "<=":
        return tb(b[1]) + " <= " + tb(b[2])
    if op == ">=":
        return tb(b[1]) + " >= " + tb(b[2])
    if op == "<":
        return tb(b[1]) + " < " + tb(b[2])
    if op == ">":
        return tb(b[1]) + " > " + tb(b[2])
    if op == "=":
        return tb(b[1]) + " == " + tb(b[2])
    if op == "!=":
        return tb(b[1]) + " != " + tb(b[2])
        
        
    if op == "not":   
        return "!= " + tb(b[1])
    if op == "and":
        for x in xrange(len(b)):
            b[x] = tb(b[x])    
        return " and ".join(b[1:])
    if op == "or":
        for x in xrange(len(b)):
            b[x] = tb(b[x])    
        return " or ".join(b[1:])        

        
    if op == "set":
        return tb(b[1]) + " = " + tb(b[2])
    if op == "+":
        for x in xrange(len(b)):
            b[x] = tb(b[x])
        return "(" + " + ".join(b[1:]) + ")"
    if op == "-":
        for x in xrange(len(b)):
            b[x] = tb(b[x])    
        return " - ".join(b[1:])
    if op == "/":
        for x in xrange(len(b)):
            b[x] = tb(b[x])    
        return "(" + " / ".join(b[1:]) + ")"
    if op == "*":
        for x in xrange(len(b)):
            b[x] = tb(b[x])    
        return " * ".join(b[1:])
    if op == "%":
        return tb(b[1]) + " % " + tb(b[2])



    if op == "len":
        return "len(" + tb(b[1]) + ")"
    if op == "sub":
        return "[" + tb(b[1]) + ":" + tb(b[2]) + "]"
    
    if op == "new_array":
        return "[]"
    if op == "new_hash":
        return "{}"
    
    if op == "ref":
        return tb(b[1]) + "[" + tb(b[2]) + "]"
         
    raise "unknown op"
    
def bad_js_translate(pcode):
    state = {}
    state["indent"] = 0
    state["give_tr"] = 1
    ocode = ""

    for block in pcode:
        ocode += bad_js_translate_block(block, state)
        
    return ocode
    
def bad_js_translate_block(b, state):
    tb = lambda b: bad_js_translate_block(b, state)

    if type(b) in [int, str]:
        return b
        
    ops = {}
    ops["if"]         = ["if_0"]  
    ops["for"]        = ["for_0"]  
    ops["def"]        = ["def_0"] 
    ops["call"]       = ["call_0"] 
    ops["inline"]     = ["inline_0"] 
    ops["ret"]        = ["ret_0"]     
    ops["not"]        = ["not_0"] 
    ops["<"]          = ["<_0"] 
    ops[">"]          = [">_0"] 
    ops["<="]         = ["<=_0"] 
    ops[">="]         = [">=_0"]     
    ops["="]          = ["=_0"] 
    ops["!="]         = ["!=_0"] 
    ops["len"]        = ["len_0"]
    ops["set"]        = ["set_0"]
    ops["+"]          = ["+_0"]
    ops["-"]          = ["-_0"]    
    ops["/"]          = ["/_0"] 
    ops["*"]          = ["*_0"]      
    ops["and"]        = ["and_0"]  
    ops["or"]         = ["or_0"]  
    ops["ref"]        = ["ref_0"]      
    ops["new_array"]  = ["new_array_0"]
    ops["new_hash"]   = ["new_hash_0"]
    ops["%"]          = ["%_0"]

    obfuscate = True

    if obfuscate:
        for v in ops.values():
            v.sort(lambda x, y: random.randint(-1, 1))

    op = ops[b[0]][0]        
    if op == "if_0":    
        ocode = "if(" + tb(b[1]) + "){"
        for block in b[2]:
            ocode += tb(block) 
        ocode += "}else{"    
        for block in b[3]:
            ocode += tb(block) 
        ocode += "}" 
        return ocode  
    if op == "for_0":
        state["give_tr"] = 0
        ocode = "for(" + tb(b[1]) + ";" + tb(b[2]) + ";" + tb(b[3]) + "){"
        state["give_tr"] = 1
        for block in b[4]:
            ocode += tb(block)
        ocode += "}"                  
        return ocode     
    if op == "for_1":
        ocode = "for(" + b[1] + ";" + b[2] + ";" + b[3] + "){"
        for block in b[4]:
            ocode += tb(block)
        ocode += "}"                  
        return ocode   

        
    if op == "def_0":
        ocode = "function " + b[1] + "(" + ", ".join(b[2][:len(b[2])]) + "){"
        for block in b[3]:
            ocode += tb(block)
        ocode += "}"    
        return ocode      
    if op == "call_0":
        return b[1] + "(" + ",".join(b[2:]) + ")"
    if op == "ret_0":
        return "return " + b[1] + ";"
    
    
    if op == "<_0":   
        return tb(b[1]) + "<" + tb(b[2])
    if op == ">_0":
        return tb(b[1]) + ">" + tb(b[2])
    if op == "<=_0":
        return tb(b[1]) + "<=" + tb(b[2])
    if op == ">=_0":
        return tb(b[1]) + ">=" + tb(b[2])
    if op == "=_0":
        return tb(b[1]) + "==" + tb(b[2])
    if op == "!=_0":
        return "(" + tb(b[1]) + "!=" + tb(b[2]) + ")" 
    if op == "%_0":
        return "(" + b[1] + "%" + b[2] + ")" 
        
        
    if op == "not_0":   
        return "!(" + tb(b[1]) + ")"
    if op == "and_0":
        for x in xrange(len(b)):
            b[x] = tb(b[x])    
        return "&&(".join(b[1:]) + ")"
    if op == "or_0":
        for x in xrange(len(b)):
            b[x] = tb(b[x])    
        return "||".join(b[1:])
    
    
    
    if op == "set_0":
        if state["give_tr"] == 1:
            tr = ";"
        else:
            tr = ""
        return tb(b[1]) + "=" + tb(b[2]) + tr
    if op == "+_0":
        for x in xrange(len(b)):
            b[x] = tb(b[x])
        return "(" + "+".join(b[1:]) + ")"
    if op == "-_0":
        for x in xrange(len(b)):
            b[x] = tb(b[x])    
        return "(" + "-".join(b[1:]) + ")"
    if op == "/_0":
        for x in xrange(len(b)):
            b[x] = tb(b[x])    
        return "(" + "/".join(b[1:]) + ")"
    if op == "*_0":
        for x in xrange(len(b)):
            b[x] = tb(b[x])    
        return "(" + "*".join(b[1:]) + ")"


    if op == "inline_0":
        return b[1]    
    if op == "len_0":
        return b[1] + ".length"        
              

    if op == "ref_0":
        return tb(b[1]) + "[" + tb(b[2]) + "]"
    if op == "new_array_0":
        return "new Array()"    
    if op == "new_hash_0":
        return "new Array()"               
        
#Псевдо код
def make_codec_simple(encoder_name, decoder_name):
    scode_enc = """(
    (def d1 (text key) (
        (set data (call crypt1 text key))
        (ret data)
    ))
    )
    """

    scode_dec = """(
    (def d1 (result_str data) (
        (set cr (new_array))
        (set j 0)
        (set l (len result_str))
        
        (inline "result_str = result_str.split('~');")
    
        (for (set i 0) (<= i l) (set i (+ i 1)) (
            (set (ref cr (ref result_str i)) '')

            (if (and (!= j 0) (= (% j 10) 0)) ( 
                (set j (+ j 1))
                (set (ref cr (ref result_str i)) (+ (ref cr (ref result_str i)) j))
            ) (
                (if (!= j 0) (
                    (set (ref cr (ref result_str i)) (+ (ref cr (ref result_str i)) j))
                ) ()) 
            ))
        (set (ref cr (ref result_str i)) (+ (ref cr (ref result_str i)) 0))
        (set j (+ j 1))
        ))
    
        (inline "d = data.replace(/0/g, '0 ').split(' ');")

        (set odata '')
        (set l (len d))
        (set ll (len result_str))
        (for (set i 0) (<= i l) (set i (+ i 1)) (
            (for (set j 0) (<= j ll) (set j (+ j 1)) (
                (if (= (ref d i) (ref cr (ref result_str j))) (
                            
                    (set odata (+ odata (ref result_str j)))
                ) ())
            ))
        ))
        (ret odata)
    )))
    """    

    encoder = []
    enc_tmp = []
    dec_tmp = []
    decoder = []

    enc_lib = read_prog(s2pcode(scode_enc)) 
    dec_lib = read_prog(s2pcode(scode_dec)) 
       
    for i in xrange(len(enc_lib)):
        v = random.randint(0,len(enc_lib)-1)
        ef, df = enc_lib[v], dec_lib[v]
        ef_func_name, df_func_name = ef[1], df[1]
    
        encoder.append(ef)
        encoder.append(["set", "text",  ["call", ef[1], "text", "key"]])
      
        decoder.insert(0, df)
        decoder.append(["set", "text", ["call", df[1], "tl", "tp"]])
        
    encoder.append(["ret", "text"])   
    decoder.append(["ret", "text"])
    enc_tmp.append(["def", encoder_name, ["text", "key"], encoder])
    encoder = enc_tmp
    dec_tmp.append(["def", decoder_name, ["tl, tp"], decoder])
    decoder = dec_tmp 

    return encoder, decoder
    
def escape(text):
    t = ""
    for i in text:
        if i == "\\": i = "\\\\"
        if i == "'": i = "\\'"
        if i == '"': i = '\\"'
        if i == "\n": i = "\\n"
        if i == "\r": i = "\\r"
        t += i
    return t

def uencode(text):
    if len(text) % 2:
        text += "\x00"

    t = ""
    for i in xrange(0, len(text), 2):
        t += "%%u%2.2x%2.2x" % (ord(text[i + 1]), ord(text[i]))

    return t
    
def code_indent(text):
    otext = ""
    s = "  "
    indent = 0
    for i in text:
        t = i
        if i == "{":
            t = " " + i + "\n"
        if i == "}":
            t = i + "\n"
        if i == ';':
            t += "\n"

        if i == "{": indent += 1
        if i == "}": indent -= 1

        if i == "}": otext = otext[:-len(s)]
        otext += t
        if t[-1] == '\n': otext += s*indent

    return otext

#Главный алгоритм
def crypt_data(data):
    encoder_name = "enc"
    decoder_name = "d" + sha.sha(str(random.randint(1001, 3000))).hexdigest()
    encoder, decoder = make_codec_simple(encoder_name, decoder_name)
    encoder = py_translate(encoder)
    decoder = bad_js_translate(decoder)
    
    dict_str, text_distrib = calc_text_distrib(data)
    dict_str = escape(dict_str)
    exec(encoder)
    exec("cdata = "+encoder_name+"(text_distrib, data)")
    cdata = escape(cdata) 
    
    odata = ""
    odata += decoder
    var_name = "z" + sha.sha(str(random.randint(1001, 3000))).hexdigest()
    odata += var_name + "="+decoder_name+"('"+dict_str+"', '"+ cdata +"');"
    odata += "eval(\"document.write("+var_name+")\");"
    
    return odata
  
if __name__ == "__main__":
    import sys
    argv = sys.argv
    argc = len(argv)
    
    if argc < 2: 
        print "Usage: crypt.py in.html [out.html]"
        sys.exit(1)
  
    f = open(argv[1], "rb")
    data = f.read(1 << 23)
    f.close()
                
    odata = crypt_data(data)
    odata = eval_shield(odata) 
    odata = eval_shield(odata) 
    odata = eval_shield(odata) 
   
    if argc == 2:
        print odata
    elif argc == 3:
        f = open(argv[2], "wb")
        f.write(odata)
        f.close()
