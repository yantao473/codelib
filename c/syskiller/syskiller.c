#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/types.h>
#include <sys/time.h>


int main(int argc, char **argv)
{
    pid_t pid;
    long numofcpus = sysconf(_SC_NPROCESSORS_ONLN);
    struct timeval tv;
    long long start_time, end_time;

    while(numofcpus--) {
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
            usleep(25000); // laod about 0.65 * cpu_counts
        }
    }

    return 0;
}
