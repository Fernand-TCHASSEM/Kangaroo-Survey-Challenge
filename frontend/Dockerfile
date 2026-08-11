# syntax=docker/dockerfile:1

FROM node:22-alpine

WORKDIR /app

# Copy only the dependency files first
COPY package.json package-lock.json* ./
RUN npm install

# The rest of the code will be mounted via a dev volume (see docker-compose.yml)
# COPY . .

EXPOSE 5173

# --host is essential for Vite to listen on all interfaces
# (otherwise, the server can only be accessed from within the container)
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
# CMD ["tail", "-f", "/dev/null"]
