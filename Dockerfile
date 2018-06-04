FROM registry.op.tiaozhan.com/tz-php7:latest

MAINTAINER dongjiangbin "dongjiangbin@tiaozhan.com"

COPY [".", "/runtime"]

RUN /usr/sbin/cbuild