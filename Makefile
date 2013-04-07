VERSION = "0.1"
VERSION2 = $(shell echo $(VERSION)|sed 's/ /-/g')
ZIPFILE = component_permissions_test_$(VERSION2).zip

FILES = *.php *.xml

all: $(ZIPFILE)

ZIPIGNORES = -x "*.zip" -x "*~" -x "*.git/*" -x "*.gitignore"

$(ZIPFILE): $(FILES)
	@echo "-------------------------------------------------------"
	@echo "Creating extension zip file: $(ZIPFILE)"
	@echo ""
	@zip -r ../$@ * $(ZIPIGNORES)
	@mv ../$@ .
	@echo "-------------------------------------------------------"
	@echo "done"
