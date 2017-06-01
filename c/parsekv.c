#include <stdio.h>
#include <stdlib.h>
#include <string.h>


typedef struct {
    char key[100];
    char val[100];
}mapnode;

char *trim(char *str)
{
	unsigned int c;
    //char *str = input;
	char *end;

	// Skip whitespace at front...
	while ((c = *str) == ' ' || c == '\t') {
		++str;
	}

	// Trim at end...
	end = str + strlen(str) - 1;
	while (end > str && ((c = *end) == ' ' || c == '\t')) {
		*end-- = 0;
	}

    return str;
}

void parsekv(char *str, char *delim, mapnode *kv){
    char *p = NULL;

    memset(kv->key, 0, sizeof(kv->key));
    memset(kv->val, 0, sizeof(kv->val));

    p = strtok(str, delim);
    if(p != NULL){
        strcpy(kv->key, trim(p));

        p = strtok(NULL, delim);
        if(p != NULL){
            strcpy(kv->val, trim(p));
        }
    }
}

int main(int argc, char *argv[]){

	if(argc != 2){
		printf("missing params\n");
	}else{
        char *delim = ":";
        mapnode *np;
        np = (mapnode *)malloc(sizeof(mapnode));
        if(NULL == np){
            return 0;
        }else{
            parsekv(argv[1], delim, np);
            printf("---%s---%s---\n", np->key, np->val);
            free(np);
            np = NULL;
        }
	}

	return 0;
}
