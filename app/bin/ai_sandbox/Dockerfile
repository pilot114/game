FROM ubuntu:latest

RUN apt-get update && apt-get install -y \
    php \
    python3 \
    python3-pip \
    bash \
    && apt-get clean

RUN ln -s /usr/bin/python3 /usr/bin/python

CMD ["/bin/bash"]