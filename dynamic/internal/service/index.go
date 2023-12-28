package service

import (
	"net/http"
)

func (s *Service) Index(w http.ResponseWriter, r *http.Request) {

	w.Write([]byte("hi, blue planet!"))
}
