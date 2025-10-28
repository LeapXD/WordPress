/* eslint-disable react/no-array-index-key */
/* eslint-disable no-underscore-dangle */
/* eslint-disable no-nested-ternary */
/* eslint-disable react/no-danger */
// File: blocks/grid-layout/edit.js
import {
  useBlockProps, useInnerBlocksProps, InspectorControls, store as blockEditorStore,
} from '@wordpress/block-editor';
import {
  TextControl,
  withFilters,
  SelectControl,
  BaseControl,
  PanelBody, ButtonGroup, Button, ToggleControl, __experimentalUnitControl as UnitControl,
  __experimentalVStack as VStack,
  __experimentalHStack as HStack,
  RangeControl,
  Popover, CardBody,
  Card,
  Icon,
} from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { useSelect, useDispatch, dispatch } from '@wordpress/data';
import {
  useEffect, useRef, useState,
} from '@wordpress/element';
import { createBlock } from '@wordpress/blocks';
import { plus } from '@wordpress/icons';
import { store as postEditorStore } from '@wordpress/edit-post';
import { store as sitetEditorStore } from '@wordpress/edit-site';

const { xd_settings: xdSettings } = window;
const { context: xdBlockContext } = xdSettings;
const { useXdBlockContext } = xdBlockContext;

const maxPx = 1000;
const maxFr = 24;
const maxPercent = 100;
const previewModes = ['mobile', 'tablet', 'desktop'];

function Edit({
  attributes,
  setAttributes,
  clientId,
}) {
  const {
    rows, rowHeights, hasModal,
  } = attributes;

  const blockContext = useXdBlockContext();
  const { contextRef, state, setState } = blockContext;

  const previewMode = useSelect((select) => (postEditorStore
    ? select(postEditorStore).__experimentalGetPreviewDeviceType()
    : sitetEditorStore
      ? select(sitetEditorStore).__experimentalGetPreviewDeviceType() : 'Desktop'), []).toLowerCase();

  const setPreviewMode = (mode) => {
    if (postEditorStore) {
      dispatch(postEditorStore).__experimentalSetPreviewDeviceType(mode);
    } else if (sitetEditorStore) {
      dispatch(sitetEditorStore).__experimentalSetPreviewDeviceType(mode);
    }
  };

  const {
    updateBlockAttributes,
    replaceInnerBlocks,
    replaceBlocks,
    __unstableMarkNextChangeAsNotPersistent: markNextChangeAsNotPersistent,
  } = useDispatch(blockEditorStore);

  const { innerBlocks, childBlocks } = useSelect((select) => {
    const { getBlocks, getBlockOrder } = select(blockEditorStore);
    return {
      innerBlocks: getBlocks(clientId) || [],
      childBlocks: getBlockOrder(clientId) || [],
    };
  }, [contextRef]);

  const otherModes = previewModes.filter((mode) => mode !== previewMode);

  const [copySource, setCopySource] = useState(otherModes[0]);
  const [popoverOpen, setPopoverOpen] = useState(false);
  const [itemsPerRow, setItemsPerRow] = useState({ mobile: 1, tablet: 2, desktop: 4 });
  const [cellsToInsert, setCellsToInsert] = useState(1);
  const [rowsToInsert, setRowsToInsert] = useState({
    count: 1,
    offset: 1,
    position: 'after',
  });

  const prevChildBlocks = useRef(childBlocks);
  const gutterSize = previewMode === 'mobile' ? 10 : 20;

  useEffect(() => {
    const newRows = { ...rows };
    const newRowHeights = { ...rowHeights };
    if (childBlocks.length !== prevChildBlocks.current.length) {
      previewModes.forEach((mode) => {
        const maxRow = innerBlocks
          .map(({
            attributes: {
              rowStart, rowSpan,
            },
          }) => (rowStart[mode] + (rowSpan[mode] - 1)))
          .sort((a, b) => a - b)
          .slice(-1)[0];

        // Only add a new row if the new block is placed on a new row
        if (maxRow > rows[mode]) {
          newRows[mode] = maxRow;
          newRowHeights[mode] = [
            ...(rowHeights[mode] || []),
            ...Array(maxRow - rows[mode]).fill('auto'),
          ];
        }
      });
      markNextChangeAsNotPersistent();
      setAttributes({
        rows: newRows,
        rowHeights: newRowHeights,
      });
    }
    prevChildBlocks.current = childBlocks;
  }, [childBlocks]);

  const handleCopyLayout = () => {
    setAttributes({
      rows: {
        ...rows,
        [previewMode]: rows[copySource],
      },
      rowHeights: {
        ...rowHeights,
        [previewMode]: rowHeights[copySource],
      },
      itemsPerRow: {
        ...itemsPerRow,
        [previewMode]: itemsPerRow[copySource],
      },
    });
    innerBlocks.forEach((block) => {
      const {
        colStart, colSpan, rowStart, rowSpan,
      } = block.attributes;
      markNextChangeAsNotPersistent();
      updateBlockAttributes(block.clientId, {
        colStart: {
          ...colStart,
          [previewMode]: colStart[copySource],
        },
        colSpan: {
          ...colSpan,
          [previewMode]: colSpan[copySource],
        },
        rowStart: {
          ...rowStart,
          [previewMode]: rowStart[copySource],
        },
        rowSpan: {
          ...rowSpan,
          [previewMode]: rowSpan[copySource],
        },
      });
    });
  };

  const handleInsertCells = (numCells) => {
    if (Number.isNaN(numCells) || numCells < 1) return;
    const newBlocks = Array.from({ length: numCells }, () => ({
      colStart: {},
      colSpan: {},
      rowStart: {},
      rowSpan: {},
    }));
    previewModes.forEach((mode) => {
      const slotSize = 24 / itemsPerRow[mode];
      const newColSpan = slotSize;
      const lastBlock = innerBlocks
        .map(({
          attributes: {
            rowStart, rowSpan, colStart, colSpan,
          }, clientId,
        }) => ({
          row: rowStart[mode] + (rowSpan[mode] - 1),
          colStart: colStart[mode],
          colSpan: colSpan[mode],
          rowStart: rowStart[mode],
          clientId,
        }))
        .sort((a, b) => a.row - b.row || a.colStart - b.colStart)
        .slice(-1)[0] || {
        row: 1, colStart: 0, colSpan: 0, rowStart: 1, clientId: null,
      };
      const lastEndCol = lastBlock.colStart + lastBlock.colSpan - 1;
      let nextSlotStart = Math.ceil(lastEndCol / slotSize) * slotSize + 1;
      let newRowStart = lastBlock.row;
      if (nextSlotStart + newColSpan - 1 > 24) {
        nextSlotStart = 1;
        newRowStart = lastBlock.row + 1;
      }
      for (let i = 0; i < numCells; i++) {
        if (nextSlotStart + newColSpan - 1 > 24) {
          nextSlotStart = 1;
          newRowStart += 1;
        }
        newBlocks[i].colStart[mode] = nextSlotStart;
        newBlocks[i].colSpan[mode] = newColSpan;
        newBlocks[i].rowStart[mode] = newRowStart;
        newBlocks[i].rowSpan[mode] = 1;
        nextSlotStart += newColSpan;
      }
    });

    replaceInnerBlocks(clientId, [
      ...innerBlocks,
      ...newBlocks.map((block) => createBlock('xd/grid-cell', block)),
    ]);
  };

  const handleInsertRows = () => {
    const insertAt = {};

    const { offset = 1, position = 'end' } = rowsToInsert || {};
    let at = 1;
    if (position === 'start') at = 1;
    else if (position === 'end') at = (rows[previewMode] || 0) + 1;
    else if (position === 'before' || position === 'after') at = offset + (position === 'after' ? 1 : 0);
    insertAt[previewMode] = at;

    const maxRowNeeded = { ...rows };
    const updatedBlocks = innerBlocks.map((block) => {
      const { rowStart } = block.attributes;
      const newRowStart = { ...rowStart };
      if (rowStart && rowStart[previewMode] >= insertAt[previewMode]) {
        newRowStart[previewMode] = rowStart[previewMode] + 1;
        const endRow = newRowStart[previewMode] + (block.attributes.rowSpan?.[previewMode] || 1) - 1;
        if (!maxRowNeeded[previewMode] || endRow > maxRowNeeded[previewMode]) {
          maxRowNeeded[previewMode] = endRow;
        }
      } else if (rowStart && rowStart[previewMode]) {
        const endRow = rowStart[previewMode] + (block.attributes.rowSpan?.[previewMode] || 1) - 1;
        if (!maxRowNeeded[previewMode] || endRow > maxRowNeeded[previewMode]) {
          maxRowNeeded[previewMode] = endRow;
        }
      }
      return block.attributes.rowStart === newRowStart
        ? createBlock(block.name, block.attributes, block.innerBlocks)
        : createBlock(block.name, { ...block.attributes, rowStart: newRowStart }, block.innerBlocks);
    });

    const newRowHeights = { ...rowHeights };
    const newRows = { ...rows };
    newRows[previewMode] = (newRows[previewMode] || 0) + 1;
    newRowHeights[previewMode] = [
      ...(newRowHeights[previewMode] || []).slice(0, insertAt[previewMode] - 1),
      ...['auto'],
      ...((newRowHeights[previewMode] || []).slice(insertAt[previewMode] - 1)),
    ];
    if (maxRowNeeded[previewMode] > newRows[previewMode]) {
      const extra = maxRowNeeded[previewMode] - newRows[previewMode];
      newRows[previewMode] = maxRowNeeded[previewMode];
      newRowHeights[previewMode] = [
        ...newRowHeights[previewMode],
        ...Array(extra).fill('auto'),
      ];
    }
    replaceBlocks(childBlocks, updatedBlocks);
    markNextChangeAsNotPersistent();
    setAttributes({
      rows: newRows,
      rowHeights: newRowHeights,
    });
  };

  const blockProps = useBlockProps({
    className: 'grid-layout',
    style: { '--grid-gap': `${gutterSize}px` },
    'data-preview-mode': previewMode,
    ref: contextRef,
  });

  const { children, ...innerBlocksProps } = useInnerBlocksProps(blockProps, {
    allowedBlocks: ['custom/grid-cell'],
    renderAppender: () => {
      if (!popoverOpen) {
        return (
          <Button
            aria-label="Add grid cells"
            className="block-editor-inserter__toggle "
            onClick={() => setPopoverOpen(true)}
            icon={<Icon className="block-editor-inserter__icon" icon={plus} />}
          />
        );
      }
      return null;
    },
  });

  return (
    <>
      {popoverOpen && (
      <Popover
        onClose={() => setPopoverOpen(false)}
        position="bottom left"
        anchor={contextRef.current}
      >
        <Card>
          <CardBody
            style={{ minWidth: '250px' }}
          >
            <TextControl
              type="number"
              label="Number of grid cells to insert."
              value={cellsToInsert}
              onChange={(value) => setCellsToInsert(parseInt(value, 10) || '')}
            />
            <BaseControl
              label="Cells Per Row"
            >
              <HStack
                gap={4}
              >
                {previewModes.map((mode, key) => (
                  <div key={key}>
                    <TextControl
                      label={mode}
                      type="number"
                      hideLabelFromVision
                      help={mode.charAt(0).toUpperCase() + mode.slice(1)}
                      onChange={(value) => {
                        setItemsPerRow({ ...itemsPerRow, [mode]: Math.min(Math.max(0, parseInt(value, 10) || 0), 24) || '' });
                      }}
                      value={itemsPerRow[mode]}
                    />
                  </div>
                ))}
              </HStack>
            </BaseControl>
          </CardBody>
          <Button
            className="block-editor-inserter__quick-inserter-expand is-next-40px-default-size"
            onClick={() => handleInsertCells(cellsToInsert)}
          >
            Insert Cells
          </Button>
        </Card>
      </Popover>
      )}
      <InspectorControls>
        <PanelBody title="Grid Settings" initialOpen>
          <ToggleControl
            label="Images open in modal"
            help="Images in this grid to open in a modal when clicked."
            checked={hasModal}
            onChange={() => setAttributes({ hasModal: !hasModal })}
          />
          <ToggleControl
            label="Show grid wireframe"
            checked={state.showGridControls ?? false}
            onChange={(value) => setState({ ...state, showGridControls: value })}
            help="Show the grid wireframe structure."
          />
          <BaseControl
            label="Preview Mode"
          >
            <ButtonGroup
              label="Preview Mode"
            >
              {['mobile', 'tablet', 'desktop'].map((mode) => (
                <Button
                  key={mode}
                  isPressed={previewMode === mode}
                  onClick={() => setPreviewMode(mode.charAt(0).toUpperCase() + mode.slice(1))}
                >
                  {mode.charAt(0).toUpperCase() + mode.slice(1)}
                </Button>
              ))}
            </ButtonGroup>
          </BaseControl>
        </PanelBody>
        <PanelBody title="Insert Grid Cells" initialOpen>
          <TextControl
            type="number"
            label="Number of grid cells to insert."
            value={cellsToInsert}
            onChange={(value) => setCellsToInsert(parseInt(value, 10) || '')}
          />
          <BaseControl
            label="Cells Per Row"
          >
            <HStack
              gap={4}
            >
              {previewModes.map((mode, key) => (
                <div key={key}>
                  <TextControl
                    label={mode}
                    type="number"
                    hideLabelFromVision
                    help={mode.charAt(0).toUpperCase() + mode.slice(1)}
                    onChange={(value) => {
                      setItemsPerRow({ ...itemsPerRow, [mode]: Math.min(Math.max(0, parseInt(value, 10) || 0), 24) || '' });
                    }}
                    value={itemsPerRow[mode]}
                  />
                </div>
              ))}
            </HStack>
            <Button
              className="block-editor-inserter__quick-inserter-expand is-next-40px-default-size"
              onClick={() => handleInsertCells(cellsToInsert)}
            >
              Insert Cells
            </Button>
          </BaseControl>
        </PanelBody>
        <PanelBody
          title="Insert Rows"
        >
          <BaseControl
            help="Insert a new row into the grid and shift cells down."
          >
            <HStack gap={4}>
              <SelectControl
                label="Position"
                value={rowsToInsert?.position || 'end'}
                options={[
                  { label: 'Before', value: 'before' },
                  { label: 'After', value: 'after' },
                  { label: 'At Start', value: 'start' },
                  { label: 'At End', value: 'end' },
                ]}
                onChange={(value) => setRowsToInsert({
                  ...rowsToInsert,
                  position: value,
                })}
              />
              <TextControl
                type="number"
                label="Row"
                value={rowsToInsert.offset}
                onChange={(value) => setRowsToInsert({
                  ...rowsToInsert,
                  offset: Math.min(Math.max(0, parseInt(value, 10) || 0), rows[previewMode] + 1) || '',
                })}
                disabled={rowsToInsert.position === 'start'
                    || rowsToInsert.position === 'end'}
              />
            </HStack>
          </BaseControl>
          <Button
            className="block-editor-inserter__quick-inserter-expand is-next-40px-default-size"
            onClick={handleInsertRows}
          >
            Insert Row
          </Button>
        </PanelBody>
        <PanelBody title="Layout Settings" initialOpen>
          <SelectControl
            label="Copy layout from"
            value={copySource}
            options={otherModes.map((mode) => ({
              label: mode.charAt(0).toUpperCase() + mode.slice(1),
              value: mode,
            }))}
            onChange={setCopySource}
            __nextHasNoMarginBottom
            style={{ marginBottom: 0 }}
          />
          <BaseControl
            help={`Copy the layout from ${copySource} mode to ${previewMode}.`}
          >
            <Button
              className="block-editor-inserter__quick-inserter-expand is-next-40px-default-size"
              onClick={handleCopyLayout}
            >
              Copy Layout
            </Button>
          </BaseControl>
        </PanelBody>
        <PanelBody title="Row Settings" initialOpen>
          <RangeControl
            label="Rows"
            value={rows[previewMode]}
            onChange={(value) => {
              const rowCountDelta = value - rows[previewMode];
              const newRowHeights = { ...rowHeights };
              newRowHeights[previewMode] = [...(rowHeights[previewMode] || [])];
              if (rowCountDelta < 0) {
                newRowHeights[previewMode] = newRowHeights[previewMode].slice(0, value);
              }
              if (rowCountDelta > 0) {
                newRowHeights[previewMode] = [
                  ...newRowHeights[previewMode],
                  ...Array(rowCountDelta).fill('auto'),
                ];
              }
              setAttributes({
                rowHeights: newRowHeights,
                rows: { ...rows, [previewMode]: value },
              });
            }}
            min={1}
            max={Math.max(24, rows[previewMode])}
          />
          <BaseControl
            label="Row Height"
          >
            <VStack>
              {Array.from({ length: rows[previewMode] }, (_, index) => (
                <PanelBody
                  title={`Row ${index + 1} Height`}
                  key={index}
                  initialOpen={false}
                  style={{ padding: 0 }}
                >
                  <SelectControl
                    value={rowHeights[previewMode]?.[index] === 'auto' ? 'auto' : 'custom'}
                    options={[
                      { label: 'Auto', value: 'auto' },
                      { label: 'Custom', value: 'custom' },
                    ]}
                    onChange={(value) => {
                      const newRowHeights = { ...rowHeights };
                      newRowHeights[previewMode] = [...(rowHeights[previewMode] || [])];
                      newRowHeights[previewMode][index] = value;
                      setAttributes({ rowHeights: newRowHeights });
                    }}
                  />
                  <HStack
                    gap={4}
                    align="center"
                    justify="flex-start"
                  >
                    {rowHeights[previewMode]?.[index] !== 'auto' && (
                      <>
                        <div
                          style={{ minWidth: '100px' }}
                        >
                          <RangeControl
                            withInputField={false}
                            value={parseInt(rowHeights[previewMode][index], 10)}
                            onChange={(value) => {
                              const newRowHeights = { ...rowHeights };
                              const prevValue = rowHeights[previewMode]?.[index];
                              const selectedUnits = prevValue
                                ?.endsWith('px') || prevValue === 'custom' ? 'px' : prevValue
                                  ?.endsWith('fr') ? 'fr' : '%';
                              newRowHeights[previewMode] = [...(rowHeights[previewMode] || [])];
                              newRowHeights[previewMode][index] = `${value}${selectedUnits}`;
                              setAttributes({ rowHeights: newRowHeights });
                            }}
                            min={0}
                            max={rowHeights[previewMode]?.[index]
                              ?.includes('fr') ? maxFr : rowHeights[previewMode]?.[index]
                                ?.includes('px') ? maxPx : maxPercent}
                          />
                        </div>

                        <UnitControl
                          value={rowHeights[previewMode]?.[index]}
                          units={[{
                            label: 'px',
                            value: 'px',
                            default: 100,
                          }, {
                            label: 'fr',
                            value: 'fr',
                            default: 1,
                          }, {
                            label: '%',
                            value: '%',
                            default: 100,
                          }]}
                          onChange={(value) => {
                            const prevValue = rowHeights[previewMode]?.[index];
                            const newRowHeights = { ...rowHeights };
                            const qty = parseInt(prevValue, 10) || 100;
                            newRowHeights[previewMode] = [...(rowHeights[previewMode] || [])];
                            newRowHeights[previewMode][index] = value
                              .endsWith('%') && qty > maxPercent ? `${maxPercent}%` : value
                                .endsWith('fr') && qty > maxFr ? `${maxFr}fr` : value
                                  .endsWith('px') && qty > maxPx ? `${maxPx}px` : value;
                            setAttributes({ rowHeights: newRowHeights });
                          }}
                        />
                      </>
                    )}
                  </HStack>
                </PanelBody>
              ))}
            </VStack>
          </BaseControl>
        </PanelBody>
      </InspectorControls>

      <div
        {...innerBlocksProps}
        style={{
          gridTemplateColumns: 'repeat(24, 1fr)',
          gridTemplateRows: rowHeights[previewMode]?.join(' '),
          gridGap: `${gutterSize}px`,
        }}
      >
        {children}
        {Array.from({ length: rows[previewMode] * 24 }, (_, index) => (
          <div
            key={index}
            style={{
              gridColumn: `${(index % 24) + 1} / span 1`,
              gridRow: `${Math.floor(index / 24) + 1} / span 1`,
              background: '#eaeaea',
              width: '100%',
              minHeight: '32px',
            }}
          />
        ))}
      </div>
    </>
  );
}

export default compose(
  withFilters('xd.innerBlocksClassName'),
  withFilters('xd.innerBlocksSettings'),
)(Edit);
