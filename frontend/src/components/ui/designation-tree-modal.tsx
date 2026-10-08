import React, { useState, useEffect } from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { ChevronRight, ChevronDown } from 'lucide-react';

interface TeamNode {
  id: number;
  name: string;
  parent_id: number | null;
  children: TeamNode[];
}

function TreeNode({ node, selectedId, onSelect, expanded, toggleExpand }: { node: TeamNode, selectedId: string | number | null, onSelect: (id: number) => void, expanded: Set<number>, toggleExpand: (id: number) => void }) {
  const hasChildren = node.children && node.children.length > 0;
  const isExpanded = expanded.has(node.id);
  const isSelected = String(node.id) === String(selectedId);

  return (
    <li className="pl-4">
      <div className="flex items-center gap-2 py-1">
        {hasChildren ? (
          <button type="button" onClick={() => toggleExpand(node.id)} className="w-4 h-4 flex items-center justify-center text-gray-500 hover:text-gray-700">
            {isExpanded ? <ChevronDown className="w-4 h-4" /> : <ChevronRight className="w-4 h-4" />}
          </button>
        ) : (
          <div className="w-4 h-4" />
        )}
        
        <input 
          type="radio" 
          checked={isSelected}
          onChange={() => onSelect(node.id)}
          className="w-4 h-4 text-blue-600 cursor-pointer"
        />
        
        <span 
          className={`cursor-pointer ${isSelected ? 'font-semibold text-blue-600' : ''}`}
          onClick={() => onSelect(node.id)}
        >
          {node.name} (Team)
        </span>
      </div>
      
      {hasChildren && isExpanded && (
        <ul className="ml-2 border-l border-gray-200">
          {node.children.map(child => (
            <TreeNode 
              key={child.id} 
              node={child} 
              selectedId={selectedId} 
              onSelect={onSelect} 
              expanded={expanded}
              toggleExpand={toggleExpand}
            />
          ))}
        </ul>
      )}
    </li>
  );
}

export function DesignationTreeModal({ isOpen, onClose, onSelect, departmentId, departments, currentValue }: any) {
  const [rootNodes, setRootNodes] = useState<TeamNode[]>([]);
  const [selectedId, setSelectedId] = useState<string | number | null>(currentValue);
  const [expanded, setExpanded] = useState<Set<number>>(new Set());

  useEffect(() => {
    if (isOpen) {
      setSelectedId(currentValue);
    }
  }, [isOpen, currentValue]);

  useEffect(() => {
    if (!departmentId || !departments) return;
    
    const dept = departments.find((d: any) => String(d.id) === String(departmentId));
    const teams = dept?.all_teams || dept?.allTeams || [];
    
    if (!teams.length) {
      setRootNodes([]);
      return;
    }

    const teamMap: { [key: number]: TeamNode } = {};
    const roots: TeamNode[] = [];

    teams.forEach((t: any) => {
      teamMap[t.id] = { ...t, children: [] };
    });

    teams.forEach((t: any) => {
      if (t.parent_id && teamMap[t.parent_id]) {
        teamMap[t.parent_id].children.push(teamMap[t.id]);
      } else {
        roots.push(teamMap[t.id]);
      }
    });

    setRootNodes(roots);
    
    // Auto expand parents of selected
    if (currentValue) {
      const newExpanded = new Set<number>();
      
      const findParents = (nodes: TeamNode[], targetId: string | number, path: number[]): boolean => {
        for (const node of nodes) {
          if (String(node.id) === String(targetId)) {
            path.forEach(p => newExpanded.add(p));
            return true;
          }
          if (node.children.length) {
            if (findParents(node.children, targetId, [...path, node.id])) {
              return true;
            }
          }
        }
        return false;
      };
      
      findParents(roots, currentValue, []);
      setExpanded(newExpanded);
    }
  }, [departmentId, departments, currentValue, isOpen]);

  const toggleExpand = (id: number) => {
    setExpanded(prev => {
      const next = new Set(prev);
      if (next.has(id)) next.delete(id);
      else next.add(id);
      return next;
    });
  };

  const handleConfirm = () => {
    if (selectedId) {
      onSelect(selectedId);
    }
    onClose();
  };

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="sm:max-w-[500px] max-h-[80vh] flex flex-col">
        <DialogHeader>
          <DialogTitle>Select Designation</DialogTitle>
        </DialogHeader>
        
        <div className="overflow-y-auto p-4 border rounded-md min-h-[200px]">
          {!departmentId ? (
            <div className="text-gray-500 italic">Please select a department first.</div>
          ) : rootNodes.length === 0 ? (
            <div className="text-gray-500 italic">No designations found for this department.</div>
          ) : (
            <ul>
              {rootNodes.map(node => (
                <TreeNode 
                  key={node.id} 
                  node={node} 
                  selectedId={selectedId} 
                  onSelect={setSelectedId} 
                  expanded={expanded}
                  toggleExpand={toggleExpand}
                />
              ))}
            </ul>
          )}
        </div>
        
        <DialogFooter>
          <Button variant="outline" onClick={onClose}>Cancel</Button>
          <Button onClick={handleConfirm} disabled={!selectedId}>Confirm</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
