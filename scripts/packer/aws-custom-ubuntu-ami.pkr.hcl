packer {
  required_plugins {
    amazon = {
      version = ">= 1.2.8"
      source  = "github.com/hashicorp/amazon"
    }
  }
}

variable "aws_access_key" {
  type = string
}

variable "aws_secret_key" {
  type = string
}

source "amazon-ebs" "ubuntu" {
  ami_name      = "laravel-custom-ubuntu-24-ami-v3"
  instance_type = "t3.medium"
  access_key    = var.aws_access_key
  secret_key    = var.aws_secret_key
  region        = "us-east-1"

  source_ami_filter {
    filters = {
      name                = "ubuntu/images/hvm-ssd-gp3/ubuntu-noble-24.04-amd64-server-20250115"
      root-device-type    = "ebs"
      virtualization-type = "hvm"
    }
    most_recent = true
    owners      = ["099720109477"]
  }
  ssh_username = "ubuntu"
}

build {
  name = "learn-packer"
  sources = [
    "source.amazon-ebs.ubuntu"
  ]

  provisioner "file" {
    source      = "../../scripts/bash"
    destination = "/tmp/scripts"
  }

  provisioner "file" {
    source      = "./scripts/bash"
    destination = "/tmp/scripts"
  }

  provisioner "shell" {
    inline = [
      "sudo snap install aws-cli --classic",
      "echo Provisioning K8S",
      "cd /tmp/scripts && chmod +x install_docker.sh && chmod +x install_k8s_locally.sh",
      "./install_docker.sh",
      "sleep 10",
      "./install_k8s_locally.sh",
      "sleep 20",
      "sudo groupadd docker",
      "sudo usermod -aG docker $USER",
      "newgrp docker",
      "sudo systemctl enable docker.service",
    ]
  }

}
