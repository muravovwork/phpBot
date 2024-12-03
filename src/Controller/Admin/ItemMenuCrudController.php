<?php

namespace App\Controller\Admin;

use App\Entity\ItemMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CurrencyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ItemMenuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ItemMenu::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name')->setLabel('Имя'),
            IntegerField::new('price')->setLabel('Цена'),
            IntegerField::new('day')->setLabel('Номер дня'),
            TextareaField::new('description')->setLabel('Описание'),
            TextField::new('photo_url')->setLabel('Ссылка на изображение'),
            TextField::new('command')->setLabel('Команда'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Меню')
            ->setEntityLabelInPlural('Меню')
            ;
    }
}
