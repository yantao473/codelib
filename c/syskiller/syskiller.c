#include <sys/sysinfo.h>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <limits.h>
#include <sys/types.h>
#include <time.h>

void kill_cpu();

int main(int argc, char **argv)
{
    struct sysinfo info;
    long d =  (1 << SI_LOAD_SHIFT );
    float real_load;
    int fork_nums = 4;
    int sleep_times = 60;

    pid_t pid;

    float max_float = fork_nums * 0.8;
    float min_float = fork_nums * 0.6;

    if(argc >= 2) {
        fork_nums = atoi(argv[1]);
    }

    while(fork_nums--) {
        if( (pid = fork()) > 0 ) {
            continue;
        }

        for(;;) {
            sysinfo(&info);
            real_load = (float) info.loads[0] / (float) d;

            if(real_load <= min_float) {
                kill_cpu();
            }

            if(real_load > max_float) {
                srand((int)time(NULL));
                sleep_times = 40 + rand() % 40;
                sleep(sleep_times);
            }
        }
    }

    return 0;
}

void kill_cpu()
{
    int i, j, k;
    int m = 1;

    for(i = 1; i < 1000; i++) {
        for(j = 1; j < 1000; j++) {
            for(k = 1; k < 1000; k++) {
                m *= i;

                if(m <= INT_MAX) {
                    m = 1;
                }
            }
        }
    }
}
