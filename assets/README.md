



when launching another image


```
[bash]
aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin 674005964842.dkr.ecr.us-east-1.amazonaws.com

docker build 
docker run
docker build -t harrison-basketball-app:latest . && docker run -p 8080:80 harrison-basketball-app:latest
docker images -> to find id
docker tag <docker image id> 674005964842.dkr.ecr.us-east-1.amazonaws.com/s3908223-cloc-a3-basketball-app:harrison-basketball-app

docker images -> find 
docker push repository:tag 
e.g., 674005964842.dkr.ecr.us-east-1.amazonaws.com/s3908223-cloc-a3-basketball-app:harrison-basketball-app

```
go to ECS 
clusters _. cluster
services -> service 
update
force new deployment








docker tag <image-id> <repository-uri>/repository-name:custom-name
docker push <repository-uri>/repository-name:custom-name
aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin <repository-uri>
```


ecs -> networking -> subnets -> sec groups -> load balancer -> 
