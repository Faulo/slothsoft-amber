cmake_minimum_required(VERSION 3.21)
project(amberworld LANGUAGES C)

set(CMAKE_C_STANDARD 99)
set(CMAKE_C_STANDARD_REQUIRED ON)
if(CMAKE_C_COMPILER_ID MATCHES "GNU|Clang")
    add_compile_options(-Wall -Wextra)
endif()

add_library(amblib STATIC ambtool/amblib.c)
target_include_directories(amblib PUBLIC ambtool)

add_executable(ambtool ambtool/ambtool.c ambtool/am_os.c)
target_link_libraries(ambtool PRIVATE amblib)

add_executable(amgfx amgfx/amgfx.c amgfx/SaveTGA8.c)

include(CTest)
if(BUILD_TESTING)
    add_subdirectory(tests)
endif()
