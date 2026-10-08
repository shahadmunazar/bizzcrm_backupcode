import React, { useEffect, useState } from 'react';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Checkbox } from '@/components/ui/checkbox';
import { apiClient } from '@/lib/api-client';
import { useParams } from 'next/navigation';

interface PermissionItem {
  id: number;
  name: string;
  module: string;
  sub_module: string;
  sub_module_key: string;
  action: string;
  action_key: string;
}

interface PermissionsTableProps {
  value?: number[];
  onChange?: (permissions: number[]) => void;
}

const ACTION_COLS = [
  { key: 'read', label: 'Read' },
  { key: 'write', label: 'Write' }, // 'write' was mentioned in the user's snippet, but some backend actions might be 'create' or 'edit'. The blade uses 'read', 'write', 'create', 'delete', 'import', 'export'
  { key: 'create', label: 'Create' },
  { key: 'update', label: 'Update' },
  { key: 'delete', label: 'Delete' },
  { key: 'import', label: 'Import' },
  { key: 'export', label: 'Export' }
];

export function PermissionsTable({ value = [], onChange }: PermissionsTableProps) {
  const params = useParams();
  const routeBoxSlug = (params?.box_slug as string) || '';

  const [permissionsData, setPermissionsData] = useState<Record<string, PermissionItem[]>>({});
  const [isLoading, setIsLoading] = useState(true);
  const [selectedIds, setSelectedIds] = useState<Set<number>>(new Set(value));

  useEffect(() => {
    setSelectedIds(new Set(value));
  }, [value]);

  const updateSelected = (newSelected: Set<number>) => {
    setSelectedIds(newSelected);
    if (onChange) {
      onChange(Array.from(newSelected));
    }
  };

  useEffect(() => {
    if (!routeBoxSlug || routeBoxSlug === '[box_slug]') return;
    const fetchPermissions = async () => {
      try {
        const boxSlug = routeBoxSlug;
        const res = await apiClient.get(`/${boxSlug}/permissions`);
        if (res?.data) {
          setPermissionsData(res.data);
        }
      } catch (error) {
        console.error('Failed to fetch permissions', error);
      } finally {
        setIsLoading(false);
      }
    };
    fetchPermissions();
  }, [routeBoxSlug]);

  const handleToggleAll = (checked: boolean) => {
    if (checked) {
      const allIds = new Set<number>();
      Object.values(permissionsData).flat().forEach(p => allIds.add(p.id));
      updateSelected(allIds);
    } else {
      updateSelected(new Set());
    }
  };

  const handleTogglePermission = (id: number, checked: boolean) => {
    const next = new Set(selectedIds);
    if (checked) next.add(id);
    else next.delete(id);
    updateSelected(next);
  };

  const handleToggleModuleRow = (perms: PermissionItem[], checked: boolean) => {
    const next = new Set(selectedIds);
    perms.forEach(p => {
      if (checked) next.add(p.id);
      else next.delete(p.id);
    });
    updateSelected(next);
  };

  if (isLoading) return <div className="p-4 text-center">Loading permissions...</div>;

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between p-4 border rounded bg-card">
        <h6 className="font-semibold">Enable Options</h6>
        <div className="flex items-center gap-6">
          <div className="flex items-center gap-2">
            <Checkbox id="select-all" checked={selectedIds.size > 0 && selectedIds.size === Object.values(permissionsData).flat().length} onCheckedChange={(checked: boolean | string) => handleToggleAll(!!checked)} />
            <Label htmlFor="select-all">Select All</Label>
          </div>
        </div>
      </div>

      <div className="border rounded overflow-x-auto overflow-y-hidden">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Module</TableHead>
              {ACTION_COLS.map(col => (
                <TableHead key={col.key}>{col.label}</TableHead>
              ))}
            </TableRow>
          </TableHeader>
          <TableBody>
            {Object.entries(permissionsData).map(([moduleName, perms]) => {
              if (moduleName === 'All Modules') return null; // handled by "Select All"
              
              // Group by sub_module
              const subModules = perms.reduce((acc, perm) => {
                const sub = perm.sub_module || 'General';
                if (!acc[sub]) acc[sub] = [];
                acc[sub].push(perm);
                return acc;
              }, {} as Record<string, PermissionItem[]>);

              return (
                <React.Fragment key={moduleName}>
                  {Object.entries(subModules).map(([subName, subPerms]) => {
                    const rowDisplayName = subName === 'General' || subName === 'All' ? moduleName : `${moduleName} - ${subName}`;
                    const isAllSubChecked = subPerms.every(p => selectedIds.has(p.id));

                    return (
                      <TableRow key={`${moduleName}-${subName}`}>
                        <TableCell>
                          <div className={`flex items-center gap-2 ${subName !== 'General' && subName !== 'All' ? 'ml-4' : ''}`}>
                            <Switch 
                              id={`switch-${moduleName}-${subName}`} 
                              checked={isAllSubChecked}
                              onCheckedChange={(checked: boolean | string) => handleToggleModuleRow(subPerms, checked as boolean)}
                            />
                            <Label htmlFor={`switch-${moduleName}-${subName}`}>{rowDisplayName}</Label>
                          </div>
                        </TableCell>
                        {ACTION_COLS.map(col => {
                          const perm = subPerms.find(p => p.action_key === col.key || p.action_key.includes(col.key));
                          return (
                            <TableCell key={col.key}>
                              {perm ? (
                                <Checkbox 
                                  checked={selectedIds.has(perm.id)}
                                  onCheckedChange={(checked: boolean | string) => handleTogglePermission(perm.id, !!checked)}
                                />
                              ) : null}
                            </TableCell>
                          );
                        })}
                      </TableRow>
                    );
                  })}
                </React.Fragment>
              );
            })}
          </TableBody>
        </Table>
      </div>
    </div>
  );
}
