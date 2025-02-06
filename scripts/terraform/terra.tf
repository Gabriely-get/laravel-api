terraform {
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 5.0"
    }
  }
}

variable "aws_access_key" {
  type = string
}

variable "aws_secret_key" {
  type = string
}

variable "ami_owner" {
  type = string
}

variable "vpc_id" {
  type = string
}

provider "aws" {
  region     = "us-east-1"
  access_key = var.aws_access_key
  secret_key = var.aws_secret_key
}

data "aws_vpc" "selected" {
  id = var.vpc_id
}

# resource "aws_acm_certificate" "cert" {
#   domain_name       = "apigabytech.com"
#   validation_method = "DNS"

#   tags = {
#     Environment = "dev"
#   }

#   lifecycle {
#     create_before_destroy = true
#   }
# }

resource "aws_ecr_repository" "laravel_api" {
  name                 = "laravel_api_registry"
  image_tag_mutability = "MUTABLE"

  image_scanning_configuration {
    scan_on_push = true
  }
}

resource "aws_security_group" "allow_laravel_api" {
  name        = "allow_laravel_api"
  description = "Allow TCP 8000 inbound traffic port"
  vpc_id      = data.aws_vpc.selected.id

  tags = {
    Name = "allow_laravel_api"
  }
}

resource "aws_vpc_security_group_ingress_rule" "allow_tcp_8000" {
  security_group_id = aws_security_group.allow_laravel_api.id
  cidr_ipv4         = "0.0.0.0/0"
  from_port         = 8000
  ip_protocol       = "tcp"
  to_port           = 8000
}

resource "aws_vpc_security_group_ingress_rule" "allow_tcp_22" {
  security_group_id = aws_security_group.allow_laravel_api.id
  cidr_ipv4         = "0.0.0.0/0"
  from_port         = 22
  ip_protocol       = "tcp"
  to_port           = 22
}

resource "aws_vpc_security_group_egress_rule" "allow_all_traffic" {
  security_group_id = aws_security_group.allow_laravel_api.id
  cidr_ipv4         = "0.0.0.0/0"
  ip_protocol       = "-1"
  from_port         = 0
  to_port           = 0
}

data "aws_ami" "ubuntu" {
  most_recent = true

  filter {
    name   = "name"
    values = ["laravel-custom-ubuntu-24-ami-v2"]
  }

  filter {
    name   = "virtualization-type"
    values = ["hvm"]
  }

  owners = [var.ami_owner]

}

resource "aws_instance" "web" {
  ami                    = data.aws_ami.ubuntu.image_id
  instance_type          = "t3.medium"
  subnet_id              = "subnet-052a800e71a631eab"
  vpc_security_group_ids = [aws_security_group.allow_laravel_api.id]

  root_block_device {
    volume_size = 35  # Tamanho do volume root em GB
    volume_type = "gp2"  # Tipo de volume (gp2 é o padrão para volumes SSD)
    delete_on_termination = true  # Apaga o volume quando a instância for terminada
  }

  tags = {
    Name = "custom-laravel-api-instance"
  }
}
