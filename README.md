# Installaing
1. git clone https://github.com/htetmyathlaing/laravel-temporal-demo.git
2. cd laravel-temporal-demo
3. composer install
4. cp .env.example .env
5. ./vendor/bin/rr get-binary 

> **Warning**<br>
> In step 5, if you are asked to create rr config yaml file, 
> please choose **NO**.
> The `rr.yaml` file is already configured in the project.
- ./rr serve 

# Run Temporal Server
1. cd temporal-server
2. docker-compose up -d

# Run RoadRunner Worker
> **Warning**<br>
> This command needed to be run under project root
1. ./rr serve

# References
- [Lara.camp presentation](https://docs.google.com/presentation/d/1-BGLwI17k7Y5OOTpofppWiVq6ax_OIxfCK_HbGKmaGM/edit?usp=sharing)
- [Temporal.io](https://temporal.io/)
- [PHP Samples](https://github.com/temporalio/samples-php)
- [Temporal 101 with Go](https://learn.temporal.io/courses/temporal_101/go)
- [Temporal 102 with Go](https://learn.temporal.io/courses/temporal_102/go)

