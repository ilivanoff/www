<?php

/**
 * Хранилище фолдингов SDK.
 * Если этот файл будет переименован, нужно его записать в config-sdk.ini
 *
 * @author azazello
 */
final class FoldingsProviderSdk extends FoldingsProviderAbstract {

    /**
     * Список фолдингов
     */
    public static function listFoldings() {
        $foldings = array();
        /*
          $managers = array(
          MagManager::inst(),
          BlogManager::inst(),
          TrainManager::inst()
          );


          foreach ($managers as $manager) {
          if ($manager instanceof RubricsProcessor) {
          $foldings[] = $manager->getRubricsFolding();
          }
          if ($manager instanceof PostsProcessor) {
          $foldings[] = $manager->getFolding();
          }
          }

          //Фолдинги
          $foldings[] = PopupPagesManager::inst();
          $foldings[] = PluginsManager::inst();
          $foldings[] = IdentPagesManager::inst();
          $foldings[] = TimeLineManager::inst();
          $foldings[] = TemplateMessages::inst();
          $foldings[] = UserPointsManager::inst();
          $foldings[] = StockManager::inst();
          $foldings[] = HelpManager::inst();
          $foldings[] = EmailManager::inst();
          $foldings[] = PSForm::inst();
          $foldings[] = DialogManager::inst();
          //Библиотеки
          $foldings[] = PoetsManager::inst();
          $foldings[] = ScientistsManager::inst();
          //Админские страницы
          $foldings[] = APagesResources::inst();
          //Базовые страницы
          $foldings[] = BasicPagesManager::inst();
          //Построитель страниц
          $foldings[] = PageBuilder::inst();
          //Управление списком предпросмотра постов
          $foldings[] = ShowcasesCtrlManager::inst();
          //Элементы в правой панели навигации
          $foldings[] = ClientBoxManager::inst();
         */
        //Все фолдинги системы
        return $foldings;
    }

}

?>