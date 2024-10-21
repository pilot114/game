CONTAINER_NAME=ai_sandbox

DOCKER_RUN=docker run --name $(CONTAINER_NAME) -itd -v ./app:/app
DOCKER_EXEC=docker exec -it $(CONTAINER_NAME)
DOCKER_STOP=docker stop $(CONTAINER_NAME)
DOCKER_RM=docker rm $(CONTAINER_NAME)

run:
	@docker build -t $(CONTAINER_NAME) .
	@$(DOCKER_RUN) $(CONTAINER_NAME) /bin/bash
	@echo "Container $(CONTAINER_NAME) is running."

stop:
	@$(DOCKER_STOP)
	@$(DOCKER_RM)
	@echo "Container $(CONTAINER_NAME) stopped and removed."

cmd:
	@$(DOCKER_EXEC) /bin/bash -c '$(CMD)' || true

.PHONY: run stop cmd
