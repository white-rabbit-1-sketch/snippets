package processor

import (
	"crawler/crawler/collector/request"
	"github.com/PuerkitoBio/goquery"
	"net/http"
	"net/url"
	"strings"
)

type UrlProcessor struct {
	urlHandleFunc func(string)
}

func NewUrlProcessor(urlHandleFunc func(string)) *UrlProcessor {
	return &UrlProcessor{
		urlHandleFunc: urlHandleFunc,
	}
}

func (p *UrlProcessor) Process(req *request.Request, resp *http.Response) {
	document, err := goquery.NewDocumentFromReader(resp.Body)
	if err != nil {
		req.MarkAsError(err.Error())
	} else {
		urlList := make([]string, 0)

		document.Find("a").Each(func(index int, element *goquery.Selection) {
			urlAddress, _ := element.Attr("href")
			urlList = append(urlList, urlAddress)
		})
		document.Find("link").Each(func(index int, element *goquery.Selection) {
			urlAddress, _ := element.Attr("href")
			urlList = append(urlList, urlAddress)
		})
		document.Find("script").Each(func(index int, element *goquery.Selection) {
			urlAddress, _ := element.Attr("src")
			urlList = append(urlList, urlAddress)
		})

		for _, urlAddress := range urlList {
			u, err := url.Parse(req.GetUrl())
			if err != nil {
				continue //don't mark main request as error
			}

			if !strings.HasPrefix(urlAddress, "http://") &&
				!strings.HasPrefix(urlAddress, "https://") &&
				!strings.Contains(urlAddress, u.Hostname()) {
				base, err := url.Parse(req.GetUrl())
				if err != nil {
					continue //don't mark main request as error
				}

				urlAddress = base.ResolveReference(&url.URL{Path: urlAddress}).String()
			}

			p.urlHandleFunc(urlAddress)
		}
	}
}
