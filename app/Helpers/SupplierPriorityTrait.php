<?php

namespace App\Helpers;

trait SupplierPriorityTrait
{
    private function updatePriority($id, $priority)
    {
        $getPriority = \App\SupplierPriority::where('id', $priority)->first();
        $getHigherPriority = \App\Supplier::where('priority', '>=', $getPriority->priority)->get();
        $selected_supplier = \App\Supplier::where('id', $id)->first();
        $updateSupplierPriority = 0;
        if ($selected_supplier) {
            $updateSupplierPriority = \App\Supplier::where('id', $id)->update(['priority' => $priority]);
        }

        $getTotalPriorities = \App\SupplierPriority::count();
        foreach ($getHigherPriority as $supplier) {
            if ($getTotalPriorities > $supplier->priority) {
                $supplier->priority += 1;
                $supplier->save();
            // $updatePriority = \App\Supplier::where('id', '!=' , $id)->where('priority','>=', $getPriority->priority)->update(['priority' => $new_priority]);
            } else {
                $supplier->priority = null;
                $supplier->save();
            //     $updatePriority = \App\Supplier::where('id', '!=' , $id)->where('priority','>=', $getPriority->priority)->update(['priority' => null]);
            }
        }

        return $updateSupplierPriority;
    }
}
