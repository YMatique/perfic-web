<?php

namespace App\Livewire;

use App\Models\Category;
use App\Traits\WithToast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;


class CategoryManager extends Component
{
    use WithToast;
public $title = 'Categorias';
public $pageTitle = 'Gestão de Categorias';

    // Form properties
    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule('required|in:income,expense')]
    public $type = 'expense';

    #[Rule('required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/')]
    public $color = '#6366f1';

    #[Rule('required|string|max:50')]
    public $icon = 'wallet';

    #[Rule('boolean')]
    public $is_active = true;

    // Component state
    public $categories;
    public $showForm = false;
    public $editingCategory = null;
    public $filterType = 'all';

    // Available SVG icons (Lucide icons available in Flux)
    // Available Material Icons
    public $availableIcons = [
        // Finanças
        'account_balance_wallet' => 'Carteira',
        'credit_card' => 'Cartão',
        'payments' => 'Pagamentos',
        'monetization_on' => 'Dinheiro',
        'savings' => 'Poupança',
        
        // Compras & Alimentação
        'shopping_cart' => 'Compras',
        'shopping_bag' => 'Sacola',
        'restaurant' => 'Restaurante',
        'local_cafe' => 'Café',
        'fastfood' => 'Fast Food',
        'local_grocery_store' => 'Mercado',
        
        // Casa & Utilidades
        'home' => 'Casa',
        'flash_on' => 'Energia',
        'water_drop' => 'Água',
        'wifi' => 'Internet',
        'phone' => 'Telefone',
        'build' => 'Manutenção',
        
        // Transporte
        'directions_car' => 'Carro',
        'local_gas_station' => 'Combustível',
        'directions_bus' => 'Ônibus',
        'flight' => 'Viagem',
        'pedal_bike' => 'Bicicleta',
        'local_taxi' => 'Taxi',
        
        // Saúde & Bem-estar
        'favorite' => 'Saúde',
        'medical_services' => 'Médico',
        'fitness_center' => 'Academia',
        'spa' => 'Beleza',
        'local_pharmacy' => 'Farmácia',
        
        // Entretenimento
        'sports_esports' => 'Jogos',
        'movie' => 'Cinema',
        'music_note' => 'Música',
        'camera_alt' => 'Fotografia',
        'tv' => 'TV/Streaming',
        'sports_soccer' => 'Esportes',
        
        // Educação & Trabalho
        'school' => 'Educação',
        'work' => 'Trabalho',
        'laptop' => 'Tecnologia',
        'book' => 'Livros',
        
        // Outros
        'card_giftcard' => 'Presentes',
        'pets' => 'Pets',
        'checkroom' => 'Roupas',
        'park' => 'Lazer',
        'child_care' => 'Crianças'
    ];


    // Available colors
    public $availableColors = [
        '#ef4444' => 'Vermelho',
        '#f97316' => 'Laranja',
        '#f59e0b' => 'Âmbar',
        '#eab308' => 'Amarelo',
        '#84cc16' => 'Lima',
        '#22c55e' => 'Verde',
        '#10b981' => 'Esmeralda',
        '#14b8a6' => 'Teal',
        '#06b6d4' => 'Ciano',
        '#0ea5e9' => 'Azul Céu',
        '#3b82f6' => 'Azul',
        '#6366f1' => 'Índigo',
        '#8b5cf6' => 'Violeta',
        '#a855f7' => 'Roxo',
        '#d946ef' => 'Fúcsia',
        '#ec4899' => 'Rosa',
        '#64748b' => 'Cinza Azul',
        '#6b7280' => 'Cinza'
    ];

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $query = Category::query()->orderBy('order')->orderBy('name');

        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }

        $this->categories = $query->get();
    }

    public function updatedFilterType()
    {
        $this->loadCategories();
    }

    public function openCreateForm()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEditForm(Category $category)
    {
        $this->resetForm();
        $this->editingCategory = $category;
        $this->name = $category->name;
        $this->type = $category->type;
        $this->color = $category->color;
        $this->icon = $category->icon;
        $this->is_active = $category->is_active;
        $this->showForm = true;
    }

    public function store()
    {
        $this->validate();

        try {
            Category::create([
                'tenant_id' => auth()->id(),
                'name' => $this->name,
                'type' => $this->type,
                'color' => $this->color,
                'icon' => $this->icon,
                'is_active' => $this->is_active,
                'order' => Category::where('tenant_id', auth()->id())->max('order') + 1,
            ]);

            $this->toastSuccess('Categoria criada!', 'A categoria foi criada com sucesso.');
            $this->resetForm();
            $this->loadCategories();
            
        } catch (\Exception $e) {
            $this->toastError('Erro!', 'Erro ao criar categoria: ' . $e->getMessage());
        }
    }

    public function update()
    {
        $this->validate();

        try {
            $this->editingCategory->update([
                'name' => $this->name,
                'type' => $this->type,
                'color' => $this->color,
                'icon' => $this->icon,
                'is_active' => $this->is_active,
            ]);

            $this->toastSuccess('Categoria atualizada!', 'A categoria foi atualizada com sucesso.');
            $this->resetForm();
            $this->loadCategories();
            
        } catch (\Exception $e) {
            $this->toastError('Erro!', 'Erro ao atualizar categoria: ' . $e->getMessage());
        }
    }

    public function delete(Category $category)
    {
        try {
            // Check if category has transactions
            if ($category->transactions()->count() > 0) {
                $this->toastWarning('Atenção!', 'Esta categoria possui transações e não pode ser excluída.');
                return;
            }

            $category->delete();
            $this->toastSuccess('Categoria excluída!', 'A categoria foi excluída com sucesso.');
            $this->loadCategories();
            
        } catch (\Exception $e) {
            $this->toastError('Erro!', 'Erro ao excluir categoria: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Category $category)
    {
        try {
            $category->update(['is_active' => !$category->is_active]);
            
            $status = $category->is_active ? 'ativada' : 'desativada';
            $this->toastSuccess('Status alterado!', "Categoria {$status} com sucesso.");
            $this->loadCategories();
            
        } catch (\Exception $e) {
            $this->toastError('Erro!', 'Erro ao alterar status da categoria.');
        }
    }

    public function resetForm()
    {
        $this->name = '';
        $this->type = 'expense';
        $this->color = '#6366f1';
        $this->icon = 'wallet';
        $this->is_active = true;
        $this->editingCategory = null;
        $this->showForm = false;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.category-manager')->layout('components.layouts.perfic-layout', [
        'title' => $this->title,
        'pageTitle' => $this->pageTitle
    ]);
    }
}
