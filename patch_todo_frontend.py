import re

file_path = '/Applications/XAMPP/xamppfiles/htdocs/newbizzcrm_v3/frontend/src/app/[box_slug]/(pages)/to-do/page.tsx'

with open(file_path, 'r') as f:
    content = f.read()

# 1. Update imports
content = content.replace("import { Pencil, Trash2, Plus } from 'lucide-react';", "import { Pencil, Trash2, Plus, Search } from 'lucide-react';")

# 2. Update state and effects
state_code = """
  const [filterTitle, setFilterTitle] = useState('');
  const [filterStatus, setFilterStatus] = useState('all');
  const [filterPriority, setFilterPriority] = useState('all');
  const [filterCategory, setFilterCategory] = useState('all');
  const [debouncedTitle, setDebouncedTitle] = useState('');

  useEffect(() => {
    const timer = setTimeout(() => {
      setDebouncedTitle(filterTitle);
    }, 500);
    return () => clearTimeout(timer);
  }, [filterTitle]);
"""
content = re.sub(r'const \[todos, setTodos\] = useState<any\[\]>\(\[\]\);', state_code + r'\n  const [todos, setTodos] = useState<any[]>([]);', content)

fetch_code = """
  const fetchTodos = async () => {
    try {
      setIsLoading(true);
      const res = await apiClient.get(`/${routeBoxSlug}/todo`, {
        params: {
          title: debouncedTitle,
          status: filterStatus,
          priority: filterPriority,
          category: filterCategory,
        }
      });
      setTodos(res?.data || []);
"""
content = re.sub(r'const fetchTodos = async \(\) => \{\n\s*try \{\n\s*setIsLoading\(true\);\n\s*const res = await apiClient\.get\(`/\$\{routeBoxSlug\}/todo`\);\n\s*setTodos\(res\?\.data \|\| \[\]\);', fetch_code, content)

content = content.replace("useEffect(() => {\n    if (routeBoxSlug) {\n      fetchTodos();\n    }\n  }, [routeBoxSlug]);", "useEffect(() => {\n    if (routeBoxSlug) {\n      fetchTodos();\n    }\n  }, [routeBoxSlug, debouncedTitle, filterStatus, filterPriority, filterCategory]);")

# 3. Add filters UI
filters_ui = """
      <div className="flex flex-wrap gap-4 mb-6">
        <div className="flex-1 min-w-[200px]">
          <Input 
            placeholder="Search by title..." 
            value={filterTitle}
            onChange={(e) => setFilterTitle(e.target.value)}
          />
        </div>
        <div className="w-[150px]">
          <Combobox value={filterStatus} onValueChange={setFilterStatus}>
            <ComboboxInput placeholder="Filter by Status" />
            <ComboboxContent>
              <ComboboxList>
                <ComboboxItem value="all">All Status</ComboboxItem>
                <ComboboxItem value="Active">Active</ComboboxItem>
                <ComboboxItem value="Completed">Completed</ComboboxItem>
                <ComboboxItem value="On-Hold">On-Hold</ComboboxItem>
              </ComboboxList>
            </ComboboxContent>
          </Combobox>
        </div>
        <div className="w-[150px]">
          <Combobox value={filterPriority} onValueChange={setFilterPriority}>
            <ComboboxInput placeholder="Filter by Priority" />
            <ComboboxContent>
              <ComboboxList>
                <ComboboxItem value="all">All Priority</ComboboxItem>
                <ComboboxItem value="High">High</ComboboxItem>
                <ComboboxItem value="Medium">Medium</ComboboxItem>
                <ComboboxItem value="Low">Low</ComboboxItem>
              </ComboboxList>
            </ComboboxContent>
          </Combobox>
        </div>
      </div>

      <div className="bg-card rounded-md border">
"""
content = content.replace('<div className="bg-card rounded-md border">', filters_ui)

with open(file_path, 'w') as f:
    f.write(content)

print("Done")
