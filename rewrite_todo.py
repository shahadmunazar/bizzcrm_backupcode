import re

content = """'use client';

import { useState, useEffect } from 'react';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
} from '@/components/ui/dialog';
import {
  Combobox,
  ComboboxInput,
  ComboboxContent,
  ComboboxItem,
  ComboboxList
} from '@/components/ui/combobox';
import { Checkbox } from '@/components/ui/checkbox';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover';
import { Collapsible, CollapsibleTrigger, CollapsibleContent } from '@/components/ui/collapsible';
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/components/ui/dropdown-menu';
import { useForm, Controller } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import { toast } from 'sonner';
import { apiClient } from '@/lib/api-client';
import { Pencil, Trash2, Plus, Search, ChevronDown, Filter, ArrowUpDown, Calendar, Settings } from 'lucide-react';
import { useParams } from 'next/navigation';
import { Badge } from '@/components/ui/badge';

const formSchema = z.object({
  id: z.number().optional(),
  title: z.string().min(1, 'Title is required'),
  category: z.string().optional(),
  start_date: z.string().min(1, 'Start date is required'),
  due_date: z.string().min(1, 'Due date is required'),
  priority: z.string().min(1, 'Priority is required'),
  status: z.string().min(1, 'Status is required'),
  description: z.string().min(1, 'Description is required'),
});

type FormValues = z.infer<typeof formSchema>;

export default function TodoPage() {
  const params = useParams();
  const routeBoxSlug = (params?.box_slug as string) || '';

  const [todos, setTodos] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [itemToDelete, setItemToDelete] = useState<number | null>(null);

  const [filterTitle, setFilterTitle] = useState('');
  const [filterStatus, setFilterStatus] = useState('all');
  const [filterPriority, setFilterPriority] = useState('all');
  const [filterCategory, setFilterCategory] = useState('all');
  const [debouncedTitle, setDebouncedTitle] = useState('');

  const [recentOpen, setRecentOpen] = useState(true);
  const [yesterdayOpen, setYesterdayOpen] = useState(true);
  const [olderOpen, setOlderOpen] = useState(true);

  useEffect(() => {
    const timer = setTimeout(() => {
      setDebouncedTitle(filterTitle);
    }, 500);
    return () => clearTimeout(timer);
  }, [filterTitle]);

  const form = useForm<FormValues>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      title: '',
      category: '',
      start_date: '',
      due_date: '',
      priority: 'Medium',
      status: 'Active',
      description: '',
    },
  });

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
    } catch (error) {
      console.error('Failed to fetch todos:', error);
      toast.error('Failed to load todos');
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    if (routeBoxSlug) {
      fetchTodos();
    }
  }, [routeBoxSlug, debouncedTitle, filterStatus, filterPriority, filterCategory]);

  const handleEdit = (item: any) => {
    form.reset({
      id: item.id,
      title: item.title,
      category: item.category || '',
      start_date: item.start_date ? item.start_date.split('T')[0] : '',
      due_date: item.due_date ? item.due_date.split('T')[0] : '',
      priority: item.priority || 'Medium',
      status: item.status || 'Active',
      description: item.description || '',
    });
    setIsModalOpen(true);
  };

  const confirmDelete = (id: number) => {
    setItemToDelete(id);
    setIsDeleteModalOpen(true);
  };

  const handleDelete = async () => {
    if (!itemToDelete) return;
    
    try {
      await apiClient.delete(`/${routeBoxSlug}/todo/${itemToDelete}`);
      toast.success('Todo deleted successfully');
      setIsDeleteModalOpen(false);
      setItemToDelete(null);
      fetchTodos();
    } catch (error: any) {
      toast.error(error?.message || 'Failed to delete todo');
    }
  };

  const onSubmit = async (data: FormValues) => {
    try {
      if (data.id) {
        await apiClient.put(`/${routeBoxSlug}/todo/${data.id}`, data);
        toast.success('Todo updated successfully');
      } else {
        await apiClient.post(`/${routeBoxSlug}/todo`, data);
        toast.success('Todo created successfully');
      }
      
      setIsModalOpen(false);
      form.reset();
      fetchTodos();
    } catch (error: any) {
      toast.error(error?.message || 'Failed to save todo');
    }
  };

  // Group Todos
  const recentTodos = todos.filter(t => {
    const d = new Date(t.created_at || t.start_date);
    const today = new Date();
    return d.toDateString() === today.toDateString();
  });
  
  const yesterdayTodos = todos.filter(t => {
    const d = new Date(t.created_at || t.start_date);
    const yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);
    return d.toDateString() === yesterday.toDateString();
  });
  
  const olderTodos = todos.filter(t => {
    const d = new Date(t.created_at || t.start_date);
    const today = new Date();
    const yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);
    return d.toDateString() !== today.toDateString() && d.toDateString() !== yesterday.toDateString();
  });

  const renderTodoTable = (items: any[]) => {
    if (items.length === 0) {
      return <div className="text-muted-foreground text-sm py-4 pl-2">No tasks in this group.</div>;
    }
    return (
      <Table className="mt-2">
        <TableHeader>
          <TableRow>
            <TableHead>Title</TableHead>
            <TableHead>Start Date</TableHead>
            <TableHead>Due Date</TableHead>
            <TableHead>Priority</TableHead>
            <TableHead>Status</TableHead>
            <TableHead className="text-right">Actions</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          {items.map((item) => (
            <TableRow key={item.id}>
              <TableCell className="font-medium">{item.title}</TableCell>
              <TableCell>{item.start_date ? new Date(item.start_date).toLocaleDateString() : '-'}</TableCell>
              <TableCell>{item.due_date ? new Date(item.due_date).toLocaleDateString() : '-'}</TableCell>
              <TableCell>
                <Badge variant={item.priority === 'High' ? 'destructive' : item.priority === 'Medium' ? 'default' : 'secondary'}>
                  {item.priority}
                </Badge>
              </TableCell>
              <TableCell>
                {item.status === 'Active' ? (
                  <Badge className="bg-green-100 text-green-700 hover:bg-green-100 border-green-200">Active</Badge>
                ) : (
                  <Badge variant="secondary">{item.status}</Badge>
                )}
              </TableCell>
              <TableCell className="text-right">
                <div className="flex justify-end gap-2">
                  <Button variant="ghost" size="icon" onClick={() => handleEdit(item)}>
                    <Pencil className="h-4 w-4" />
                  </Button>
                  <Button variant="ghost" size="icon" onClick={() => confirmDelete(item.id)}>
                    <Trash2 className="h-4 w-4 text-destructive" />
                  </Button>
                </div>
              </TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    );
  };

  return (
    <div className="p-6 bg-slate-50 min-h-screen">
      {/* Top Header matching UI */}
      <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b pb-4">
        <div className="flex items-center">
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <h1 className="text-xl font-bold cursor-pointer flex items-center gap-1 hover:text-gray-700">
                All Tasks <ChevronDown className="h-5 w-5" />
              </h1>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="start">
              <DropdownMenuItem>All Tasks</DropdownMenuItem>
              <DropdownMenuItem>My Tasks</DropdownMenuItem>
              <DropdownMenuItem>Completed Tasks</DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>

        <div className="flex items-center gap-3 flex-wrap">
          <div className="flex items-center space-x-2 mr-2">
            <Checkbox id="mark-read" />
            <label htmlFor="mark-read" className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-gray-500">
              Mark all as read
            </label>
          </div>

          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="outline" size="sm" className="bg-white">
                <ArrowUpDown className="mr-2 h-4 w-4" /> Sort <ChevronDown className="ml-2 h-4 w-4" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent>
              <DropdownMenuItem>Newest First</DropdownMenuItem>
              <DropdownMenuItem>Oldest First</DropdownMenuItem>
              <DropdownMenuItem>Priority (High to Low)</DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>

          <Button variant="outline" size="sm" className="bg-white">
            <Calendar className="mr-2 h-4 w-4 text-gray-500" />
            <span className="text-gray-600">09/21/2026 - 09/27/2026</span>
          </Button>

          <Popover>
            <PopoverTrigger asChild>
              <Button variant="outline" size="sm" className="bg-white">
                <Filter className="mr-2 h-4 w-4" /> Filter
              </Button>
            </PopoverTrigger>
            <PopoverContent className="w-80" align="end">
              <div className="space-y-4">
                <h4 className="font-medium leading-none mb-4">Filter Tasks</h4>
                <div className="space-y-2">
                  <Label>Search Title</Label>
                  <Input 
                    placeholder="Search..." 
                    value={filterTitle}
                    onChange={(e) => setFilterTitle(e.target.value)}
                  />
                </div>
                <div className="space-y-2">
                  <Label>Status</Label>
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
                <div className="space-y-2">
                  <Label>Priority</Label>
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
            </PopoverContent>
          </Popover>

          <Button variant="destructive" size="icon" className="bg-red-500 hover:bg-red-600">
            <Settings className="h-4 w-4" />
          </Button>
          
          <Button onClick={() => {
            form.reset({
              title: '',
              category: '',
              start_date: '',
              due_date: '',
              priority: 'Medium',
              status: 'Active',
              description: '',
            });
            setIsModalOpen(true);
          }} className="ml-2">
            <Plus className="mr-2 h-4 w-4" /> Add New Task
          </Button>
        </div>
      </div>

      {/* Grouped Lists */}
      <div className="bg-white rounded-md border p-4 shadow-sm min-h-[500px]">
        {isLoading ? (
          <div className="text-center py-12 text-muted-foreground">Loading tasks...</div>
        ) : (
          <div className="space-y-6">
            <Collapsible open={recentOpen} onOpenChange={setRecentOpen} className="border-b pb-4">
              <CollapsibleTrigger className="flex items-center justify-between w-full hover:bg-slate-50 p-2 rounded-md transition-colors">
                <div className="flex items-center gap-3">
                  <h3 className="font-bold text-lg">Recent</h3>
                  <Badge variant="outline" className="rounded-full bg-slate-100 text-slate-500">{recentTodos.length}</Badge>
                </div>
                <ChevronDown className={`h-5 w-5 transition-transform duration-200 ${recentOpen ? 'rotate-180 text-red-500' : 'text-slate-400'}`} />
              </CollapsibleTrigger>
              <CollapsibleContent className="pt-2">
                {renderTodoTable(recentTodos)}
              </CollapsibleContent>
            </Collapsible>

            <Collapsible open={yesterdayOpen} onOpenChange={setYesterdayOpen} className="border-b pb-4">
              <CollapsibleTrigger className="flex items-center justify-between w-full hover:bg-slate-50 p-2 rounded-md transition-colors">
                <div className="flex items-center gap-3">
                  <h3 className="font-bold text-lg text-slate-800">Yesterday</h3>
                  <Badge variant="outline" className="rounded-full bg-slate-100 text-slate-500">{yesterdayTodos.length}</Badge>
                </div>
                <ChevronDown className={`h-5 w-5 transition-transform duration-200 ${yesterdayOpen ? 'rotate-180 text-slate-800' : 'text-slate-400'}`} />
              </CollapsibleTrigger>
              <CollapsibleContent className="pt-2">
                {renderTodoTable(yesterdayTodos)}
              </CollapsibleContent>
            </Collapsible>
            
            <Collapsible open={olderOpen} onOpenChange={setOlderOpen} className="pb-4">
              <CollapsibleTrigger className="flex items-center justify-between w-full hover:bg-slate-50 p-2 rounded-md transition-colors">
                <div className="flex items-center gap-3">
                  <h3 className="font-bold text-lg text-slate-800">Older</h3>
                  <Badge variant="outline" className="rounded-full bg-slate-100 text-slate-500">{olderTodos.length}</Badge>
                </div>
                <ChevronDown className={`h-5 w-5 transition-transform duration-200 ${olderOpen ? 'rotate-180 text-slate-800' : 'text-slate-400'}`} />
              </CollapsibleTrigger>
              <CollapsibleContent className="pt-2">
                {renderTodoTable(olderTodos)}
              </CollapsibleContent>
            </Collapsible>
          </div>
        )}
      </div>

      {/* Add/Edit Modal */}
      <Dialog open={isModalOpen} onOpenChange={setIsModalOpen}>
        <DialogContent className="max-w-md">
          <DialogHeader>
            <DialogTitle>{form.watch('id') ? 'Edit Task' : 'Create New Task'}</DialogTitle>
          </DialogHeader>
          
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="title">Task Title <span className="text-destructive">*</span></Label>
              <Input 
                id="title" 
                placeholder="Enter task title" 
                {...form.register('title')} 
              />
              {form.formState.errors.title && (
                <p className="text-sm text-destructive">{form.formState.errors.title.message}</p>
              )}
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="start_date">Start Date <span className="text-destructive">*</span></Label>
                <Input 
                  id="start_date" 
                  type="date"
                  {...form.register('start_date')} 
                />
                {form.formState.errors.start_date && (
                  <p className="text-sm text-destructive">{form.formState.errors.start_date.message}</p>
                )}
              </div>
              
              <div className="space-y-2">
                <Label htmlFor="due_date">Due Date <span className="text-destructive">*</span></Label>
                <Input 
                  id="due_date" 
                  type="date"
                  {...form.register('due_date')} 
                />
                {form.formState.errors.due_date && (
                  <p className="text-sm text-destructive">{form.formState.errors.due_date.message}</p>
                )}
              </div>
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label>Priority <span className="text-destructive">*</span></Label>
                <Controller
                  control={form.control}
                  name="priority"
                  render={({ field }) => (
                    <Combobox 
                      value={field.value} 
                      onValueChange={field.onChange}
                    >
                      <ComboboxInput placeholder="Select priority" />
                      <ComboboxContent>
                        <ComboboxList>
                          <ComboboxItem value="High">High</ComboboxItem>
                          <ComboboxItem value="Medium">Medium</ComboboxItem>
                          <ComboboxItem value="Low">Low</ComboboxItem>
                        </ComboboxList>
                      </ComboboxContent>
                    </Combobox>
                  )}
                />
              </div>

              <div className="space-y-2">
                <Label>Status <span className="text-destructive">*</span></Label>
                <Controller
                  control={form.control}
                  name="status"
                  render={({ field }) => (
                    <Combobox 
                      value={field.value} 
                      onValueChange={field.onChange}
                    >
                      <ComboboxInput placeholder="Select status" />
                      <ComboboxContent>
                        <ComboboxList>
                          <ComboboxItem value="Active">Active</ComboboxItem>
                          <ComboboxItem value="Completed">Completed</ComboboxItem>
                          <ComboboxItem value="On-Hold">On-Hold</ComboboxItem>
                        </ComboboxList>
                      </ComboboxContent>
                    </Combobox>
                  )}
                />
              </div>
            </div>

            <div className="space-y-2">
              <Label htmlFor="description">Description <span className="text-destructive">*</span></Label>
              <Textarea 
                id="description" 
                placeholder="Enter task details..."
                className="resize-none h-24"
                {...form.register('description')} 
              />
              {form.formState.errors.description && (
                <p className="text-sm text-destructive">{form.formState.errors.description.message}</p>
              )}
            </div>

            <DialogFooter className="pt-4">
              <Button type="button" variant="outline" onClick={() => setIsModalOpen(false)}>
                Cancel
              </Button>
              <Button type="submit">
                {form.watch('id') ? 'Update Task' : 'Create Task'}
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>

      {/* Delete Confirmation Modal */}
      <Dialog open={isDeleteModalOpen} onOpenChange={setIsDeleteModalOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Delete Task</DialogTitle>
          </DialogHeader>
          <div className="py-4">
            <p>Are you sure you want to delete this task? This action cannot be undone.</p>
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setIsDeleteModalOpen(false)}>
              Cancel
            </Button>
            <Button variant="destructive" onClick={handleDelete}>
              Delete
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
"""

with open('/Applications/XAMPP/xamppfiles/htdocs/newbizzcrm_v3/frontend/src/app/[box_slug]/(pages)/to-do/page.tsx', 'w') as f:
    f.write(content)
