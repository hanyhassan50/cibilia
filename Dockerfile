# Building on top of Ubuntu 14.04. The best distro around.
FROM ubuntu:16.04

COPY ./cibilia /opt/
EXPOSE 8080

ENTRYPOINT ["/opt/cibilia"]
