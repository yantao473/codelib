#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#include <sys/types.h>
#include <sys/socket.h>
#include <arpa/inet.h> 

int is_valid_ip(const char *ipstr){
    struct sockaddr_in sa;
    int result = inet_pton(AF_INET, ipstr, &(sa.sin_addr));
    if (result == 0) {
        return result; 
    } 
    return 1;
}

int main(int argc, char *argv[]){
    if(argc < 2){
        printf("Usage: %s ip:port", argv[0]);
        exit(0);
    }

    printf("is valid ip: %d\n", is_valid_ip(argv[1]));

    return 0;
}
