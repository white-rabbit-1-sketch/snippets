package processor

import (
	"bytes"
	"crawler/crawler/collector/request"
	"crypto/md5"
	"encoding/hex"
	"io/ioutil"
	"net/http"
	"os"
)

type SnapshotProcessor struct {
	snapshotDirPath string
}

func NewSnapshotProcessor(snapshotDirPath string) *SnapshotProcessor {
	return &SnapshotProcessor{
		snapshotDirPath: snapshotDirPath,
	}
}

func (p *SnapshotProcessor) Process(req *request.Request, resp *http.Response) {
	file, err := os.Create(p.snapshotDirPath + "/" + buildMd5(req.GetUrl()))
	if err != nil {
		req.MarkAsError(err.Error())
		return
	}
	defer file.Close()

	bodyBytes, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		req.MarkAsError(err.Error())
		return
	}

	_, err = file.Write(bodyBytes)
	if err != nil {
		req.MarkAsError(err.Error())
		return
	}

	resp.Body = ioutil.NopCloser(bytes.NewReader(bodyBytes))
}

func buildMd5(str string) string {
	data := []byte(str)
	hash := md5.Sum(data)

	return hex.EncodeToString(hash[:])
}
