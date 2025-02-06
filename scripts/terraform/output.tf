output "ecr_registry_id" {
  value = aws_ecr_repository.laravel_api.repository_url
}

# terraform output ecr_registry_id
