package request

const (
	StateReady    = 1
	StateQueued   = 2
	StateVisited  = 3
	StateFiltered = 4
	StateCanceled = 5
	StateError    = -1
)

var stateTitleMap = map[int]string{
	StateReady:    "READY",
	StateQueued:   "QUEUED",
	StateVisited:  "VISITED",
	StateFiltered: "FILTERED",
	StateCanceled: "CANCELED",
	StateError:    "ERROR",
}

type Request struct {
	Url          string
	State        int
	StateDetails string
}

func NewRequest(url string) *Request {
	request := &Request{
		Url:   url,
		State: StateReady,
	}

	return request
}

func (r *Request) GetUrl() string {
	return r.Url
}

func (r *Request) GetState() int {
	return r.State
}

func (r *Request) GetStateTitle() string {
	return stateTitleMap[r.State]
}

func (r *Request) GetStateDetails() string {
	return r.StateDetails
}

func (r *Request) MarkAsReady() {
	r.State = StateReady
}

func (r *Request) MarkAsQueued() {
	r.State = StateQueued
}

func (r *Request) MarkAsVisited() {
	r.State = StateVisited
}

func (r *Request) MarkAsFiltered(details string) {
	r.State = StateFiltered
	r.StateDetails = details
}

func (r *Request) MarkAsError(details string) {
	r.State = StateError
	r.StateDetails = details
}

func (r *Request) MarkAsCanceled() {
	r.State = StateCanceled
}

func (r *Request) IsReady() bool {
	return r.State == StateReady
}

func (r *Request) IsQueued() bool {
	return r.State == StateQueued
}

func (r *Request) IsVisited() bool {
	return r.State == StateVisited
}

func (r *Request) IsFiltered() bool {
	return r.State == StateFiltered
}

func (r *Request) IsError() bool {
	return r.State == StateError
}

func (r *Request) IsCanceled() bool {
	return r.State == StateCanceled
}
