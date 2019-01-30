#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/types.h>
#include <sys/time.h>


int main(int argc, char **argv)
{
    pid_t pid;
    int fork_nums = 4;
    struct timeval tv;
    long long start_time, end_time;


    if(argc >= 2) {
        fork_nums = atoi(argv[1]);
    }

    while(fork_nums--) {
        if( (pid = fork()) > 0 ) {
            continue;
        }

        while(1) {
            gettimeofday(&tv, NULL);
            start_time = tv.tv_sec * 1000000 + tv.tv_usec;
            end_time = start_time;

            while((end_time - start_time) < 60000) {
                gettimeofday(&tv, NULL);
                end_time = tv.tv_sec * 1000000 + tv.tv_usec;
            }
            /* usleep(60000); */
            usleep(35000); // laod about 0.65 * cpu_counts
        }
    }

    return 0;
}
