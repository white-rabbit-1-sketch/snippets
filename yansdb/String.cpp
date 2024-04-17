#include "String.hpp"

std::string trim(const std::string& str) {
    size_t first = str.find_first_not_of(" \r\n");
    if (first == std::string::npos) {
        return "";
    }
    size_t last = str.find_last_not_of(" \r\n");
    return str.substr(first, last - first + 1);
}

std::vector<std::string> explode(const std::string &str, char delimiter) 
{
    std::vector<std::string> result;
    std::istringstream stringStream(str);
    std::string token;
    
    while (std::getline(stringStream, token, delimiter)) {
        result.push_back(trim(token));
    }
    
    return result;
}