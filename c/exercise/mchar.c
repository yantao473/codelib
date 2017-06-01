#include <stdio.h>

#define IN 1
#define OUT 0

int main () {
    int c, nline, nword, nchar, state;
    state = OUT;
    nline = 0;
    nword = 0;
    nchar = 0;

    while((c = getchar()) != EOF) {
        ++nchar;

        if(c == '\n') {
            ++nline;
        }
        if(c == ' ' || c == '\n' || c == '\t') {
            state = OUT;
        } else if(state == OUT) {
            state = IN;
            ++nword;
        }
    }

    printf("%d %d %d\n", nline, nword, nchar);
    return 0;
}
