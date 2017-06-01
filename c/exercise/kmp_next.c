#include <stdio.h>

void get_next(char *t, int *next) {
    int i, j;
    i = 1;
    j = 0;
    next[1] = 0;
    while(i < t[0]) {
        if(j == 0 || t[i] == t[j]) {
            ++i;
            ++j;
            next[i] = j;
        } else {
            j = next[j];
        }
    }
}

int index_kmp(char *s, char *t, int pos) {
    int i = pos;
    int j = 1;
    int next[255];
    get_next(t, next);

    int k;
    for(k = 0; k < 255; k++) {
        printf("%d\n", next[k]);
    }

    while(i <= s[0] && j <= t[0]) {
        if(j == 0 || s[i] == t[j]) {
            ++i;
            ++j;
        } else {
            j = next[j];
        }

    }
    if(j > t[0]) {
        return i - t[0];
    } else {
        return 0;
    }
}

int main() {
    char s[] = "abcabcabc";
    char t[] = "abcabx";

    index_kmp(s, t, 0);
    return 0;
}
