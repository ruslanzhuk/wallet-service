PROTO_DIR=protos
OUT_DIR=src/Grpc

generate:
	@echo "🔧 Generation PHP & gRPC classes from $(PROTO_DIR)/wallet.proto..."
	docker run --rm \
		-v $(PWD):/app \
		-w /app \
		znly/protoc \
		-I $(PROTO_DIR) \
		--php_out=$(OUT_DIR) \
		--grpc_out=$(OUT_DIR) \
		--plugin=protoc-gen-grpc=/usr/bin/grpc_php_plugin \
		$(PROTO_DIR)/wallet.proto
	@echo "✅ Generated files:"
	@find $(OUT_DIR) -type f -name "*.php" -exec ls -lh {} \;