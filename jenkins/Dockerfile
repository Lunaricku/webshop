FROM jenkins/jenkins:lts

USER root

# Cài Docker CLI
RUN apt-get update && apt-get install -y docker.io docker-compose

# Cho phép Jenkins dùng Docker
RUN usermod -aG docker jenkins

USER jenkins
