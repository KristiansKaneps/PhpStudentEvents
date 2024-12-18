FROM httpd:2.4.62

# Sites for which to generate certificates (space separated)
ARG secure_sites="student-events.local"

RUN apt-get update; \
    apt-get install openssl

RUN mkdir /etc/ssl/docker-private; mkdir /etc/ssl/docker-certs

# Generate certificate for each secure site
RUN for site in $secure_sites; do \
    echo "Generating certificate for site '$site':"; \
    openssl req -x509 -newkey rsa:4096 -nodes -days 10000 \
        -keyout /etc/ssl/docker-private/$site.key -out /etc/ssl/docker-certs/$site.pem \
        -subj "/C=LV/ST=NRW/L=Riga/O=localhost/OU=development/emailAddress=webmaster@localhost/CN=$site" \
        -addext "subjectAltName=DNS:localhost,DNS:*.$site,IP:127.0.0.1"; \
    echo "Certificate for site '$site' generated!"; \
    done;
