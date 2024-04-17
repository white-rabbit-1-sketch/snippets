package filter

import (
	"crawler/crawler/collector/request"
)

type FilterInterface interface {
	Filter(req *request.Request)
}
