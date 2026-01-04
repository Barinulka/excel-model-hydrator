FROM golang:1.25-alpine AS builder

WORKDIR /app
COPY . .
RUN go mod download
RUN CGO_ENABLED=0 GOOS=linux go build -o excel-hydrator .

# ---
FROM alpine:latest
RUN apk --no-cache add ca-certificates
WORKDIR /root/
COPY --from=builder /app/excel-hydrator .
RUN mkdir -p /app/output
EXPOSE 8080
CMD ["./excel-hydrator"]
