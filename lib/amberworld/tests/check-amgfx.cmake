file(MAKE_DIRECTORY "${WORK_DIR}")
set(OUTPUT_FILE "${WORK_DIR}/${ENTRY}.tga")
file(REMOVE "${OUTPUT_FILE}")

execute_process(
    COMMAND ${EMULATOR} "${AMGFX}" "${FIXTURE_ROOT}/archive/${ENTRY}"
        -out "${OUTPUT_FILE}" -width 16 -bpl 5 -off 0
        -pal "${PALETTE}" -col 0 -size 160
    RESULT_VARIABLE RESULT
    OUTPUT_VARIABLE OUTPUT
    ERROR_VARIABLE ERROR
)
if(NOT RESULT EQUAL 0)
    message(FATAL_ERROR "amgfx failed (${RESULT}): ${OUTPUT}${ERROR}")
endif()
if(NOT EXISTS "${OUTPUT_FILE}")
    message(FATAL_ERROR "amgfx did not create ${OUTPUT_FILE}: ${OUTPUT}${ERROR}")
endif()

execute_process(
    COMMAND "${CMAKE_COMMAND}" -E compare_files
        "${FIXTURE_ROOT}/ambgfx/${ENTRY}/0/${PALETTE}.tga" "${OUTPUT_FILE}"
    RESULT_VARIABLE RESULT
)
if(NOT RESULT EQUAL 0)
    message(FATAL_ERROR "Converted ${ENTRY}.tga differs from the fixture")
endif()
