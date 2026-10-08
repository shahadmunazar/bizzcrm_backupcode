import re

file_path = '/Applications/XAMPP/xamppfiles/htdocs/newbizzcrm_v3/frontend/src/app/[box_slug]/(pages)/to-do/page.tsx'

with open(file_path, 'r') as f:
    content = f.read()

# Replace 1: All Tasks Trigger
content = content.replace('''          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <h1 className="text-xl font-bold cursor-pointer flex items-center gap-1 hover:text-gray-700">
                All Tasks <ChevronDown className="h-5 w-5" />
              </h1>
            </DropdownMenuTrigger>''', 
'''          <DropdownMenu>
            <DropdownMenuTrigger className="text-xl font-bold cursor-pointer flex items-center gap-1 hover:text-gray-700 bg-transparent border-none outline-none">
              All Tasks <ChevronDown className="h-5 w-5" />
            </DropdownMenuTrigger>''')

# Replace 2: Sort Trigger
content = content.replace('''          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="outline" size="sm" className="bg-white">
                <ArrowUpDown className="mr-2 h-4 w-4" /> Sort <ChevronDown className="ml-2 h-4 w-4" />
              </Button>
            </DropdownMenuTrigger>''',
'''          <DropdownMenu>
            <DropdownMenuTrigger className="inline-flex items-center justify-center whitespace-nowrap rounded-md font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-8 px-3 text-xs bg-white">
              <ArrowUpDown className="mr-2 h-4 w-4" /> Sort <ChevronDown className="ml-2 h-4 w-4" />
            </DropdownMenuTrigger>''')

# Replace 3: Filter Trigger
content = content.replace('''          <Popover>
            <PopoverTrigger asChild>
              <Button variant="outline" size="sm" className="bg-white">
                <Filter className="mr-2 h-4 w-4" /> Filter
              </Button>
            </PopoverTrigger>''',
'''          <Popover>
            <PopoverTrigger className="inline-flex items-center justify-center whitespace-nowrap rounded-md font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-8 px-3 text-xs bg-white">
              <Filter className="mr-2 h-4 w-4" /> Filter
            </PopoverTrigger>''')


with open(file_path, 'w') as f:
    f.write(content)

print("Done fixing triggers")
