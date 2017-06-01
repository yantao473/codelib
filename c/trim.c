#include <stdio.h>
#include <stdlib.h>
#include <string.h>

//char *trim(char *input)
//{
//	unsigned int c;
//    char *str = input;
//	char *end;
//
//	// Skip whitespace at front...
//	while ((c = *str) == ' ' || c == '\t') {
//		++str;
//	}
//
//	// Trim at end...
//	end = str + strlen(str) - 1;
//	while (end > str && ((c = *end) == ' ' || c == '\t')) {
//		*end-- = 0;
//	}
//
//    return str;
//}

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

int main(int argc, char *argv[]){
	if(argc != 2){

	}else{
		printf("---%s---\n", trim(argv[1]));
	}

	return 0;
}
