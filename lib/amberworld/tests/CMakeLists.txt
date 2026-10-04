set(FIXTURE_ROOT "${PROJECT_SOURCE_DIR}/../../test-files/2Icon_gfx")

add_executable(amblib_test amblib_test.c)
target_link_libraries(amblib_test PRIVATE amblib)
add_test(NAME amblib-format COMMAND amblib_test "${FIXTURE_ROOT}/2Icon_gfx.amb")

add_test(
    NAME ambtool-extraction
    COMMAND "${CMAKE_COMMAND}"
        "-DAMBTOOL=$<TARGET_FILE:ambtool>"
        "-DEMULATOR=${CMAKE_CROSSCOMPILING_EMULATOR}"
        "-DFIXTURE_ROOT=${FIXTURE_ROOT}"
        "-DWORK_DIR=${CMAKE_CURRENT_BINARY_DIR}/ambtool-extraction"
        -P "${CMAKE_CURRENT_SOURCE_DIR}/check-ambtool.cmake"
)

foreach(CASE IN ITEMS "003;49" "004;6")
    list(GET CASE 0 ENTRY)
    list(GET CASE 1 PALETTE)
    add_test(
        NAME "amgfx-${ENTRY}-palette-${PALETTE}"
        COMMAND "${CMAKE_COMMAND}"
            "-DAMGFX=$<TARGET_FILE:amgfx>"
            "-DEMULATOR=${CMAKE_CROSSCOMPILING_EMULATOR}"
            "-DFIXTURE_ROOT=${FIXTURE_ROOT}"
            "-DWORK_DIR=${CMAKE_CURRENT_BINARY_DIR}/amgfx-${ENTRY}-${PALETTE}"
            "-DENTRY=${ENTRY}"
            "-DPALETTE=${PALETTE}"
            -P "${CMAKE_CURRENT_SOURCE_DIR}/check-amgfx.cmake"
    )
endforeach()
