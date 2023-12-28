package main

import (
	"fmt"
	"log"
	"net/http"
	"vernarch/internal/config"
	"vernarch/internal/service"
)

const HUGO_DYNAMIC_PORT = 6660

func main() {

	cfg, err := config.Configuration()
	if err != nil {
		log.Fatalf("Error reading application configuration: %s", err)
	}

	service, err := service.NewService(cfg)
	if err != nil {
		log.Fatalf("Error creating new service: %s", err)
	}

	httpServer := &http.Server{
		Addr:    fmt.Sprintf("localhost:%d", HUGO_DYNAMIC_PORT),
		Handler: service.Muxer,
	}

	if err := httpServer.ListenAndServe(); err != nil {
		log.Fatalf("Error running http server: %s", err)
	}
}
