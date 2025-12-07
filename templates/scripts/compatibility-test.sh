#!/bin/bash

# Compatibility test script
# This script can be used to run compatibility tests manually or in CI/CD pipelines

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}PHP Compatibility Tester${NC}"
echo "================================"
echo ""

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo -e "${RED}Error: Composer is not installed${NC}"
    exit 1
fi

# Check if compatibility-tester is available
if ! command -v vendor/bin/compatibility-tester &> /dev/null; then
    echo -e "${YELLOW}Installing dependencies...${NC}"
    composer install
fi

# Run tests
echo -e "${YELLOW}Running compatibility tests...${NC}"
vendor/bin/compatibility-tester test

# Generate report
echo -e "${YELLOW}Generating report...${NC}"
vendor/bin/compatibility-tester report --format=markdown --output=compatibility-report.md

if [ -f "compatibility-report.md" ]; then
    echo -e "${GREEN}Report generated: compatibility-report.md${NC}"
else
    echo -e "${RED}Failed to generate report${NC}"
    exit 1
fi

echo -e "${GREEN}Compatibility tests completed!${NC}"

