CC=gcc
CFLAGS=-O2 -Wall -ggdb
OBJS=main.o memory_pool.o 
TARGETS=memory_pool

all: $(TARGETS)

memory_pool: $(OBJS)
	$(CC) -o $@ $(OBJS)

clean:
	rm -f *.o $(TARGETS)
