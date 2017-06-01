#include <stdio.h>
#include <string.h>

#define MAX_SIZE 100

typedef struct {
    char key[MAX_SIZE];
    char value[MAX_SIZE];
} dict;

char *skip_ws(char *str)
{
    unsigned int c;

    while ((c = *str) == ' '  ||  c == '\t'){
        str++;
    }

    return str;
}

char *get_quoted(char **from, int delim, char *to, int max)
{
    unsigned int c;
    int	k;

    to[0] = k = 0;
    max -= 2;

    while ((c = **from) != 0) {
        *from += 1;
        if (c == delim)
            break;

        if (k < max)
            to[k++] = c;
    }

    to[k] = 0;
    return (to);
}

char *noctrl(char *buffer)
{
    int	len, i;
    unsigned char *p;

    if ((p = buffer) == NULL)
        return (NULL);

    len = strlen(p);
    for (i=len-1; i>=0; i--) {
        if (p[i] <= 32)
            p[i] = '\0';
        else
            break;
    }

    return (p);
}

void parsekv(char **from, int delim, dict *stu)
{
    unsigned int c;
    int	k = 0;
    char buf[MAX_SIZE] = {0};

    while ((c = **from) != 0) {
        *from += 1;
        if (c == delim){
            break;
        }
        buf[k++] = c;
    }

    buf[k] = '\0';
    strcpy(stu->key, buf);
    strcpy(stu->value, *from);
}

int main(int argc, char *argv[]) {
    int i, k = 0;

    char *q, *p = argv[1];
    char pattern[MAX_SIZE];
    char arr[MAX_SIZE][MAX_SIZE];

    dict kvstu[MAX_SIZE];

    memset(&arr, 0, sizeof(arr));
    memset(&kvstu, 0, sizeof(kvstu));

    while (p = skip_ws(p), *get_quoted(&p, ',', pattern, sizeof(pattern)) != 0) {
        strcpy(arr[k++], pattern);
    }

    for(i = 0; i < k && i < MAX_SIZE; i++){
        q = arr[i];
        parsekv(&q, '=', &kvstu[i]);
        printf("key: %s, value: %s\n", kvstu[i].key, kvstu[i].value);
    }

    return 0;
}
