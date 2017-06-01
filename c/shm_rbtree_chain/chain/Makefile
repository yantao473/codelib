CC = gcc
CFLAGS = -Wall -ggdb
OBJS = main.o chain.o
TARGETS = chain

all: $(TARGETS)

chain: $(OBJS)
	$(CC) -o $@ $(OBJS)

clean:
	rm -f  main *.o $(TARGETS)
