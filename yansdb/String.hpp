#ifndef STRING_H
#define STRING_H

#include <sstream>
#include <string>
#include <vector>

std::string trim(const std::string& str);
std::vector<std::string> explode(const std::string &str, char delimiter);

#endif