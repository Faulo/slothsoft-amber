set(OUTPUT_DIR "${WORK_DIR}/archive")
file(REMOVE_RECURSE "${OUTPUT_DIR}")
file(MAKE_DIRECTORY "${WORK_DIR}")

execute_process(
    COMMAND ${EMULATOR} "${AMBTOOL}" "${FIXTURE_ROOT}/2Icon_gfx.amb" "${OUTPUT_DIR}"
    RESULT_VARIABLE RESULT
    OUTPUT_VARIABLE OUTPUT
    ERROR_VARIABLE ERROR
)
if(NOT RESULT EQUAL 0)
    message(FATAL_ERROR "ambtool failed (${RESULT}): ${OUTPUT}${ERROR}")
endif()
if(NOT OUTPUT MATCHES "Format: AMNP \\(compressed/encrypted archive\\)")
    message(FATAL_ERROR "ambtool reported an unexpected format: ${OUTPUT}")
endif()

file(GLOB EXPECTED_FILES RELATIVE "${FIXTURE_ROOT}/archive" "${FIXTURE_ROOT}/archive/*")
file(GLOB ACTUAL_FILES RELATIVE "${OUTPUT_DIR}" "${OUTPUT_DIR}/*")
if(NOT ACTUAL_FILES STREQUAL EXPECTED_FILES)
    message(FATAL_ERROR "Archive entries differ: expected ${EXPECTED_FILES}, got ${ACTUAL_FILES}")
endif()

foreach(FILE IN LISTS EXPECTED_FILES)
    execute_process(
        COMMAND "${CMAKE_COMMAND}" -E compare_files
            "${FIXTURE_ROOT}/archive/${FILE}" "${OUTPUT_DIR}/${FILE}"
        RESULT_VARIABLE RESULT
    )
    if(NOT RESULT EQUAL 0)
        message(FATAL_ERROR "Extracted ${FILE} differs from the fixture")
    endif()
endforeach()
